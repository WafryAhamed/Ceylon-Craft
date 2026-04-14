import { useAuthStore } from '@/stores/authStore';

/**
 * Guard for protecting routes that require authentication
 */
export const requireAuth = async (to, from, next) => {
  const authStore = useAuthStore();
  
  // Initialize auth if not done
  if (!authStore.isAuthenticated && !authStore.user) {
    authStore.initializeAuth();
  }

  if (authStore.isAuthenticated) {
    next();
  } else {
    // Redirect to login with return URL
    next({
      path: '/login',
      query: { redirect: to.fullPath },
    });
  }
};

/**
 * Guard for protecting admin routes
 */
export const requireAdmin = async (to, from, next) => {
  const authStore = useAuthStore();
  
  // Initialize auth if not done
  if (!authStore.isAuthenticated && !authStore.user) {
    authStore.initializeAuth();
  }

  if (authStore.isAuthenticated && authStore.isAdmin) {
    next();
  } else if (!authStore.isAuthenticated) {
    next({
      path: '/login',
      query: { redirect: to.fullPath },
    });
  } else {
    // Redirect to home if not admin
    next('/');
  }
};

/**
 * Guard to prevent authenticated users from accessing auth pages
 */
export const requireGuest = async (to, from, next) => {
  const authStore = useAuthStore();
  
  // Initialize auth if not done
  if (!authStore.isAuthenticated && !authStore.user) {
    authStore.initializeAuth();
  }

  if (authStore.isAuthenticated) {
    // Redirect to home if already logged in
    next('/');
  } else {
    next();
  }
};

/**
 * Global route meta guard handler
 */
export const setupRouteGuards = (router) => {
  router.beforeEach(async (to, from, next) => {
    // Check meta requirements
    if (to.meta?.requiresAuth) {
      await requireAuth(to, from, next);
    } else if (to.meta?.requiresAdmin) {
      await requireAdmin(to, from, next);
    } else if (to.meta?.requiresGuest) {
      await requireGuest(to, from, next);
    } else {
      next();
    }
  });

  // Update page title
  router.afterEach((to) => {
    document.title = to.meta?.title || 'Ceylon Craft';
  });
};
