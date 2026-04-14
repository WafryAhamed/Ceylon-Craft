# 🎨 FRONTEND INTEGRATION GUIDE - Payment System & Order Tracking

This guide explains how to integrate the Stripe payment system and order tracking into your Vue 3 frontend.

—

## 🔗 Pinia Store for Payment (`stores/paymentStore.js`)

Add this to your Pinia stores to manage payment state:

```javascript
import { defineStore } from 'pinia';
import { useApi } from '@/composables/useApi';

export const usePaymentStore = defineStore('payment', {
  state: () => ({
    paymentIntent: null,
    paymentStatus: null,
    isProcessing: false,
    error: null,
    clientSecret: null,
  }),

  computed: {
    isPaymentPending: (state) => state.paymentStatus === 'pending',
    isPaymentSucceeded: (state) => state.paymentStatus === 'succeeded',
    isPaymentFailed: (state) => state.paymentStatus === 'failed',
  },

  actions: {
    /**
     * Create payment intent
     */
    async createPaymentIntent(amount, orderId, description = 'Ceylon Craft Order') {
      this.isProcessing = true;
      this.error = null;

      try {
        const { data, error } = await useApi().call(
          'post',
          '/payments/intent',
          {
            amount,
            order_id: orderId,
            description,
            currency: 'usd',
          }
        );

        if (error) {
          this.error = error;
          return null;
        }

        this.paymentIntent = data;
        this.clientSecret = data.client_secret;
        this.paymentStatus = 'pending';

        return data;
      } catch (err) {
        this.error = err.message;
        return null;
      } finally {
        this.isProcessing = false;
      }
    },

    /**
     * Confirm payment with Stripe
     */
    async confirmPayment(paymentIntentId, paymentMethodId) {
      this.isProcessing = true;
      this.error = null;

      try {
        const { data, error } = await useApi().call(
          'post',
          '/payments/confirm',
          {
            payment_intent_id: paymentIntentId,
            payment_method_id: paymentMethodId,
          }
        );

        if (error) {
          this.error = error;
          this.paymentStatus = 'failed';
          return null;
        }

        this.paymentStatus = data.status;
        return data;
      } catch (err) {
        this.error = err.message;
        this.paymentStatus = 'failed';
        return null;
      } finally {
        this.isProcessing = false;
      }
    },

    /**
     * Get payment status
     */
    async getPaymentStatus(paymentId) {
      const { data, error } = await useApi().call('get', `/payments/${paymentId}`);

      if (!error) {
        this.paymentStatus = data.status;
      }

      return data;
    },

    /**
     * Reset payment state
     */
    resetPayment() {
      this.paymentIntent = null;
      this.paymentStatus = null;
      this.isProcessing = false;
      this.error = null;
      this.clientSecret = null;
    },
  },
});
```

—

## 💳 Stripe.js Integration (`composables/useStripe.js`)

Create a composable to handle Stripe instances and payment methods:

```javascript
import { ref } from 'vue';
import { loadStripe } from '@stripe/js';

export const useStripe = () => {
  const stripe = ref(null);
  const elements = ref(null);

  /**
   * Initialize Stripe
   */
  const initStripe = async () => {
    stripe.value = await loadStripe(
      import.meta.env.VITE_STRIPE_PUBLIC_KEY
    );
    elements.value = stripe.value.elements();
  };

  /**
   * Create card element
   */
  const createCardElement = () => {
    if (!elements.value) return null;

    const cardElement = elements.value.create('card', {
      style: {
        base: {
          fontSize: '16px',
          color: '#32325d',
          '::placeholder': { color: '#aab7c4' },
        },
        invalid: {
          color: '#fa755a',
        },
      },
    });

    return cardElement;
  };

  /**
   * Mount card element to DOM
   */
  const mountCardElement = (elementId) => {
    const cardElement = createCardElement();
    if (cardElement && document.getElementById(elementId)) {
      cardElement.mount(`#${elementId}`);
    }
    return cardElement;
  };

  /**
   * Create payment method
   */
  const createPaymentMethod = async (cardElement) => {
    if (!stripe.value) await initStripe();

    const { paymentMethod, error } = await stripe.value.createPaymentMethod({
      type: 'card',
      card: cardElement,
    });

    return { paymentMethod, error };
  };

  /**
   * Confirm card payment
   */
  const confirmCardPayment = async (clientSecret, paymentMethod) => {
    if (!stripe.value) await initStripe();

    const { paymentIntent, error } = await stripe.value.confirmCardPayment(
      clientSecret,
      {
        payment_method: paymentMethod.id,
        return_url: `${window.location.origin}/checkout/confirm`,
      }
    );

    return { paymentIntent, error };
  };

  /**
   * Handle 3D Secure payment
   */
  const handleCardAction = async (clientSecret) => {
    if (!stripe.value) await initStripe();

    const { paymentIntent, error } = await stripe.value.handleCardAction(
      clientSecret
    );

    return { paymentIntent, error };
  };

  return {
    stripe,
    elements,
    initStripe,
    createCardElement,
    mountCardElement,
    createPaymentMethod,
    confirmCardPayment,
    handleCardAction,
  };
};
```

—

## 🛒 Updated Checkout Component (`pages/Checkout.vue`)

```vue
<template>
  <div class="checkout-container">
    <!-- Checkout Form -->
    <form @submit.prevent="handleCheckout" class="checkout-form">
      <!-- Shipping Info Section -->
      <section class="section">
        <h2>Shipping Information</h2>
        <div class="form-grid">
          <input v-model="form.shipping_address" type="text" placeholder="Street Address" required />
          <input v-model="form.shipping_city" type="text" placeholder="City" required />
          <input v-model="form.shipping_postal_code" type="text" placeholder="Postal Code" required />
          <input v-model="form.shipping_phone" type="tel" placeholder="Phone Number" required />
        </div>
      </section>

      <!-- Stripe Card Element -->
      <section class="section">
        <h2>Payment Method</h2>
        <div id="card-element" class="card-element"></div>
        <div v-if="stripeError" class="error">{{ stripeError }}</div>
      </section>

      <!-- Order Summary -->
      <section class="section">
        <h2>Order Summary</h2>
        <div class="summary">
          <div class="summary-row">
            <span>Subtotal:</span>
            <span>${{ (cart.subtotal || 0).toFixed(2) }}</span>
          </div>
          <div class="summary-row">
            <span>Tax (10%):</span>
            <span>${{ (cart.tax || 0).toFixed(2) }}</span>
          </div>
          <div class="summary-row">
            <span>Shipping:</span>
            <span>${{ (cart.shippingCost || 10).toFixed(2) }}</span>
          </div>
          <div class="summary-row total">
            <span>Total:</span>
            <span>${{ (cart.total || 0).toFixed(2) }}</span>
          </div>
        </div>
      </section>

      <!-- Submit Button -->
      <button 
        type="submit" 
        :disabled="isProcessing || payment.isProcessing"
        class="btn btn-primary btn-lg"
      >
        {{ isProcessing ? 'Processing...' : `Pay $${(cart.total || 0).toFixed(2)}` }}
      </button>
    </form>

    <!-- Order Status (After Checkout) -->
    <div v-if="currentOrder" class="order-status">
      <h2>Order #{{ currentOrder.id }}</h2>
      <div class="status-tracker">
        <div 
          v-for="status in orderStatuses" 
          :key="status"
          :class="['status-step', { completed: isStatusCompleted(status) }]"
        >
          {{ status }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '@/stores/cartStore';
import { useOrderStore } from '@/stores/orderStore';
import { usePaymentStore } from '@/stores/paymentStore';
import { useUIStore } from '@/stores/uiStore';
import { useStripe } from '@/composables/useStripe';
import { useApi } from '@/composables/useApi';

const router = useRouter();
const cartStore = useCartStore();
const orderStore = useOrderStore();
const paymentStore = usePaymentStore();
const uiStore = useUIStore();
const { mountCardElement, createPaymentMethod, confirmCardPayment } = useStripe();

const form = ref({
  shipping_address: '',
  shipping_city: '',
  shipping_postal_code: '',
  shipping_phone: '',
});

const isProcessing = ref(false);
const stripeError = ref(null);
const cardElement = ref(null);
const currentOrder = ref(null);

const cart = computed(() => ({
  items: cartStore.items,
  subtotal: cartStore.subtotal,
  tax: cartStore.tax,
  shippingCost: cartStore.shippingCost,
  total: cartStore.total,
}));

const payment = computed(() => ({
  isProcessing: paymentStore.isProcessing,
  error: paymentStore.error,
}));

const orderStatuses = ['pending', 'confirmed', 'packed', 'shipped', 'delivered'];

const isStatusCompleted = (status) => {
  return orderStatuses.indexOf(status) <= orderStatuses.indexOf(currentOrder.value?.status);
};

onMounted(() => {
  // Mount Stripe card element
  cardElement.value = mountCardElement('card-element');
});

const handleCheckout = async () => {
  isProcessing.value = true;
  stripeError.value = null;

  try {
    // Step 1: Create order via checkout endpoint
    const { data: orderData, error: orderError } = await useApi().call(
      'post',
      '/orders/checkout',
      {
        shipping_address: form.value.shipping_address,
        shipping_city: form.value.shipping_city,
        shipping_postal_code: form.value.shipping_postal_code,
        shipping_phone: form.value.shipping_phone,
        payment_method: 'stripe',
        terms_agreed: true,
      }
    );

    if (orderError) {
      stripeError.value = orderError;
      uiStore.error('Failed to create order');
      return;
    }

    const orderId = orderData.order_id;
    currentOrder.value = orderData;

    // Step 2: Create payment intent
    const intentData = await paymentStore.createPaymentIntent(
      cart.value.total,
      orderId,
      `Order #${orderId} - Ceylon Craft`
    );

    if (!intentData) {
      stripeError.value = paymentStore.error;
      uiStore.error('Failed to create payment');
      return;
    }

    // Step 3: Create payment method
    const { paymentMethod, error: pmError } = await createPaymentMethod(cardElement.value);

    if (pmError) {
      stripeError.value = pmError.message;
      uiStore.error('Invalid card');
      return;
    }

    // Step 4: Confirm payment
    const confirmData = await paymentStore.confirmPayment(
      intentData.intent_id,
      paymentMethod.id
    );

    if (!confirmData) {
      stripeError.value = paymentStore.error;
      uiStore.error('Payment failed');
      return;
    }

    // Step 5: Check payment status
    if (paymentStore.isPaymentSucceeded) {
      uiStore.success('Payment successful! Order confirmed.');
      
      // Clear cart
      cartStore.clearCart();

      // Redirect to order confirmation
      setTimeout(() => {
        router.push(`/order/${orderId}`);
      }, 2000);
    } else if (confirmData.status === 'requires_action') {
      // Handle 3D Secure
      uiStore.warning('Additional verification required');
      // Implement 3D Secure handling
    } else {
      stripeError.value = 'Payment processing. Please check your email for updates.';
    }
  } catch (err) {
    stripeError.value = err.message;
    uiStore.error(`Checkout error: ${err.message}`);
  } finally {
    isProcessing.value = false;
  }
};
</script>

<style scoped>
.checkout-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
}

.section {
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: #f5f5f5;
  border-radius: 8px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.form-grid input {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.card-element {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: white;
}

.error {
  color: #dc3545;
  margin-top: 0.5rem;
}

.summary {
  background: white;
  padding: 1rem;
  border-radius: 4px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.summary-row.total {
  font-weight: bold;
  font-size: 1.25rem;
  border-top: 2px solid #ddd;
  padding-top: 0.5rem;
  margin-top: 0.5rem;
}

.status-tracker {
  display: flex;
  justify-content: space-between;
  margin: 2rem 0;
}

.status-step {
  flex: 1;
  text-align: center;
  padding: 1rem;
  background: #f0f0f0;
  margin: 0 0.5rem;
  border-radius: 4px;
  opacity: 0.5;
  transition: all 0.3s;
}

.status-step.completed {
 opacity: 1;
  background: #4CAF50;
  color: white;
}
</style>
```

—

## 📍 Order Tracking Page (`pages/OrderTracking.vue`)

```vue
<template>
  <div class="order-tracking">
    <div v-if="order" class="order-details">
      <h1>Order #{{ order.id }}</h1>
      
      <!-- Status Timeline -->
      <div class="timeline">
        <div 
          v-for="(history, index) in order.statusHistory" 
          :key="history.id"
          :class="['timeline-item', { 'is-active': index === 0 }]"
        >
          <div class="timeline-marker"></div>
          <div class="timeline-content">
            <div class="status-badge" :class="`status-${history.status}`">
              {{ formatStatus(history.status) }}
            </div>
            <p class="timestamp">{{ formatDate(history.createdAt) }}</p>
            <p v-if="history.notes" class="notes">{{ history.notes }}</p>
            <p v-if="history.trackingNumber" class="tracking">
              Tracking: {{ history.trackingNumber }}
            </p>
          </div>
        </div>
      </div>

      <!-- Order Items -->
      <div class="order-items">
        <h2>Items</h2>
        <div v-for="item in order.items" :key="item.id" class="item">
          <img :src="item.product.image" :alt="item.product.name" />
          <div class="item-details">
            <h3>{{ item.product.name }}</h3>
            <p>Qty: {{ item.quantity }} x ${{ item.price.toFixed(2) }}</p>
            <p class="total">${{ (item.quantity * item.price).toFixed(2) }}</p>
          </div>
        </div>
      </div>

      <!-- Total -->
      <div class="order-total">
        <h3>Order Total: ${{ order.total.toFixed(2) }}</h3>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useApi } from '@/composables/useApi';

const route = useRoute();
const order = ref(null);

const formatStatus = (status) => {
  const labels = {
    pending: 'Pending',
    confirmed: 'Confirmed',
    packed: 'Packed',
    shipped: 'Shipped',
    delivered: 'Delivered',
    cancelled: 'Cancelled',
  };
  return labels[status] || status;
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

onMounted(async () => {
  const { data } = await useApi().call(
    'get',
    `/orders/${route.params.id}`
  );
  order.value = data;
});
</script>

<style scoped>
.order-tracking {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
}

.timeline {
  position: relative;
  padding: 2rem 0;
}

.timeline-item {
  display: flex;
  margin-bottom: 2rem;
}

.timeline-marker {
  width: 20px;
  height: 20px;
  background: #ccc;
  border-radius: 50%;
  margin-right: 2rem;
  margin-top: 0.25rem;
  flex-shrink: 0;
}

.timeline-item.is-active .timeline-marker {
  background: #4CAF50;
  box-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
}

.timeline-content {
  flex: 1;
}

.status-badge {
  display: inline-block;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-weight: bold;
  margin-bottom: 0.5rem;
}

.status-pending { background: #FFA726; color: white; }
.status-confirmed { background: #29B6F6; color: white; }
.status-packed { background: #AB47BC; color: white; }
.status-shipped { background: #EC407A; color: white; }
.status-delivered { background: #4CAF50; color: white; }
.status-cancelled { background: #EF5350; color: white; }

.timestamp {
  color: #666;
  font-size: 0.9rem;
}

.notes, .tracking {
  margin-top: 0.5rem;
  color: #333;
}

.order-items {
  margin-top: 3rem;
  padding-top: 2rem;
  border-top: 1px solid #ddd;
}

.item {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  padding: 1rem;
  background: #f9f9f9;
  border-radius: 4px;
}

.item img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 4px;
}

.item-details h3 {
  margin: 0 0 0.5rem 0;
}

.item-details p {
  margin: 0.25rem 0;
  color: #666;
}

.item-details .total {
  font-weight: bold;
  color: #333;
  margin-top: 0.5rem;
}

.order-total {
  margin-top: 2rem;
  padding: 1.5rem;
  background: #f0f0f0;
  border-radius: 4px;
  text-align: right;
}
</style>
```

—

## 🔧 Environment Variables (`VITE_` prefix for frontend)

Add to `.env`:

```
VITE_STRIPE_PUBLIC_KEY=pk_test_YOUR_PUBLIC_KEY
VITE_API_URL=http://localhost:8000/api
```

—

## 📦 Install Stripe.js Package

```bash
npm install @stripe/js
```

---

**Status**: Ready for frontend integration  
**Next**: Test payment flow end-to-end
