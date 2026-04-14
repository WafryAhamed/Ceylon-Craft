import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import API from '@/services/api';

export const useCartStore = defineStore('cart', () => {
  const items = ref([]);
  const loading = ref(false);
  const error = ref(null);

  // Computed properties
  const itemCount = computed(() => 
    items.value.reduce((total, item) => total + item.quantity, 0)
  );

  const subtotal = computed(() =>
    items.value.reduce((total, item) => total + (item.product.price * item.quantity), 0)
  );

  const tax = computed(() => subtotal.value * 0.1); // 10% tax

  const shippingCost = computed(() => {
    if (subtotal.value === 0) return 0;
    if (subtotal.value > 100) return 0; // Free shipping over $100
    return 10; // Flat rate $10
  });

  const total = computed(() => subtotal.value + tax.value + shippingCost.value);

  // Fetch cart
  const fetchCart = async () => {
    loading.value = true;
    error.value = null;
    try {
      const response = await API.get('/cart');
      items.value = response.data.items || [];
      return items.value;
    } catch (err) {
      error.value = err.message || 'Failed to fetch cart';
      // Silently fail - cart might be empty
      items.value = [];
    } finally {
      loading.value = false;
    }
  };

  // Add to cart
  const addToCart = async (productId, quantity = 1) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await API.post('/cart/add', {
        product_id: productId,
        quantity,
      });
      // Refresh cart
      await fetchCart();
      return response;
    } catch (err) {
      error.value = err.message || 'Failed to add product';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Update cart item quantity
  const updateQuantity = async (cartItemId, quantity) => {
    if (quantity < 1) {
      return removeFromCart(cartItemId);
    }

    loading.value = true;
    error.value = null;
    try {
      await API.put(`/cart/items/${cartItemId}`, { quantity });
      await fetchCart();
    } catch (err) {
      error.value = err.message || 'Failed to update quantity';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Remove from cart
  const removeFromCart = async (cartItemId) => {
    loading.value = true;
    error.value = null;
    try {
      await API.delete(`/cart/items/${cartItemId}`);
      await fetchCart();
    } catch (err) {
      error.value = err.message || 'Failed to remove item';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Clear cart
  const clearCart = async () => {
    loading.value = true;
    error.value = null;
    try {
      await API.post('/cart/clear');
      items.value = [];
    } catch (err) {
      error.value = err.message || 'Failed to clear cart';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Get cart as local JSON (for before checkout)
  const getCartSummary = () => ({
    items: items.value,
    itemCount: itemCount.value,
    subtotal: subtotal.value.toFixed(2),
    tax: tax.value.toFixed(2),
    shippingCost: shippingCost.value.toFixed(2),
    total: total.value.toFixed(2),
  });

  return {
    // State
    items,
    loading,
    error,
    
    // Computed
    itemCount,
    subtotal,
    tax,
    shippingCost,
    total,
    
    // Actions
    fetchCart,
    addToCart,
    updateQuantity,
    removeFromCart,
    clearCart,
    getCartSummary,
  };
});
