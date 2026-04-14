import { defineStore } from 'pinia';
import { ref } from 'vue';
import API from '@/services/api';

export const useOrderStore = defineStore('order', () => {
  const orders = ref([]);
  const currentOrder = ref(null);
  const loading = ref(false);
  const error = ref(null);

  // Fetch user orders
  const fetchOrders = async () => {
    loading.value = true;
    error.value = null;
    try {
      const response = await API.get('/orders');
      orders.value = response.data || [];
      return orders.value;
    } catch (err) {
      error.value = err.message || 'Failed to fetch orders';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Fetch single order
  const fetchOrder = async (orderId) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await API.get(`/orders/${orderId}`);
      currentOrder.value = response.data;
      return currentOrder.value;
    } catch (err) {
      error.value = err.message || 'Order not found';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Create order from cart
  const checkout = async (shippingData) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await API.post('/orders/checkout', {
        shipping_address: shippingData.address,
        city: shippingData.city,
        postal_code: shippingData.postalCode,
        payment_method: shippingData.paymentMethod,
      });
      currentOrder.value = response.data;
      return response;
    } catch (err) {
      error.value = err.message || 'Checkout failed';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Cancel order
  const cancelOrder = async (orderId) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await API.post(`/orders/${orderId}/cancel`);
      await fetchOrders();
      return response;
    } catch (err) {
      error.value = err.message || 'Failed to cancel order';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Fetch all orders (admin)
  const fetchAllOrders = async (filters = {}) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await API.get('/admin/orders', { params: filters });
      orders.value = response.data || [];
      return orders.value;
    } catch (err) {
      error.value = err.message || 'Failed to fetch orders';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Update order status (admin)
  const updateOrderStatus = async (orderId, status) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await API.put(`/admin/orders/${orderId}/status`, { status });
      await fetchOrders();
      return response;
    } catch (err) {
      error.value = err.message || 'Failed to update order status';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Get order statistics (admin)
  const getOrderStats = async () => {
    try {
      const response = await API.get('/admin/orders/stats');
      return response.data || {};
    } catch (err) {
      console.error('Failed to fetch order stats:', err);
      return {};
    }
  };

  return {
    // State
    orders,
    currentOrder,
    loading,
    error,
    
    // Actions
    fetchOrders,
    fetchOrder,
    checkout,
    cancelOrder,
    fetchAllOrders,
    updateOrderStatus,
    getOrderStats,
  };
});
