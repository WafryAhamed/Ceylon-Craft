import { useAuthStore } from '@/stores/authStore';

/**
 * Guard for protecting routes that require authentication
 */
export const requireAuth = async (to, from) => {
  const authStore = useAuthStore();
  
  // Initialize auth if not done
  if (!authStore.isAuthenticated && !authStore.user) {
    authStore.initializeAuth();
  }

  if (authStore.isAuthenticated) {
    return true;
  } else {
    // Redirect to login with return URL
    return {
      path: '/login',
      query: { redirect: to.fullPath },
    };
  }
};

/**
 * Guard for protecting admin routes
 */
export const requireAdmin = async (to, from) => {
  const authStore = useAuthStore();
  
  // Initialize auth if not done
  if (!authStore.isAuthenticated && !authStore.user) {
    authStore.initializeAuth();
  }

  if (authStore.isAuthenticated && authStore.isAdmin) {
    return true;
  } else if (!authStore.isAuthenticated) {
    return {
      path: '/login',
      query: { redirect: to.fullPath },
    };
  } else {
    // Redirect to home if not admin
    return '/';
  }
};

/**
 * Guard to prevent authenticated users from accessing auth pages
 */
export const requireGuest = async (to, from) => {
  const authStore = useAuthStore();
  
  // Initialize auth if not done
  if (!authStore.isAuthenticated && !authStore.user) {
    authStore.initializeAuth();
  }

  if (authStore.isAuthenticated) {
    // Redirect to home if already logged in
    return '/';
  } else {
    return true;
  }
};

/**
 * Global route meta guard handler
 */
export const setupRouteGuards = (router) => {
  router.beforeEach(async (to, from) => {
    // Check meta requirements
    if (to.meta?.requiresAuth) {
      return await requireAuth(to, from);
    } else if (to.meta?.requiresAdmin) {
      return await requireAdmin(to, from);
    } else if (to.meta?.requiresGuest) {
      return await requireGuest(to, from);
    }
    return true;
  });

  // Update page title
  router.afterEach((to) => {
    document.title = to.meta?.title || 'Ceylon Craft';
  });
};
