import { createRouter, createWebHistory } from 'vue-router';
import { setupRouteGuards } from '../middleware/routeGuards.js';

// Pages
import Home from '../pages/Home.vue';
import Products from '../pages/Products.vue';
import ProductDetail from '../pages/ProductDetail.vue';
import Categories from '../pages/Categories.vue';
import CategoryDetail from '../pages/CategoryDetail.vue';
import About from '../pages/About.vue';
import Contact from '../pages/Contact.vue';
import Blog from '../pages/Blog.vue';
import BlogPost from '../pages/BlogPost.vue';
import Login from '../pages/Login.vue';
import Register from '../pages/Register.vue';
import Cart from '../pages/Cart.vue';
import Checkout from '../pages/Checkout.vue';
import Wishlist from '../pages/Wishlist.vue';
import Profile from '../pages/Profile.vue';
import Orders from '../pages/Orders.vue';
import OrderDetail from '../pages/OrderDetail.vue';
import SearchResults from '../pages/SearchResults.vue';
import PrivacyPolicy from '../pages/PrivacyPolicy.vue';
import Terms from '../pages/Terms.vue';
import ShippingReturns from '../pages/ShippingReturns.vue';
import FAQ from '../pages/FAQ.vue';
import NotFound from '../pages/NotFound.vue';

// Admin Pages
import AdminDashboard from '../pages/admin/Dashboard.vue';
import AdminProducts from '../pages/admin/Products.vue';
import AdminOrders from '../pages/admin/Orders.vue';
import AdminCategories from '../pages/admin/Categories.vue';

const routes = [
  {
    path: '/',
    component: Home,
    meta: {
      title: 'Ceylon Craft | Handmade Products from Sri Lanka',
      description: 'Discover unique handcrafted products from Sri Lankan artisans. Premium quality handmade home decor, art, and gifts.'
    }
  },
  {
    path: '/products',
    component: Products,
    meta: {
      title: 'Products | Ceylon Craft - Shop Handmade Items',
      description: 'Browse our collection of handmade products including home decor, art, journals, and unique gifts from Sri Lanka.'
    }
  },
  {
    path: '/product/:slug',
    component: ProductDetail,
    meta: {
      title: 'Product Details | Ceylon Craft',
      description: 'View detailed product information, reviews, and related items.'
    }
  },
  {
    path: '/categories',
    component: Categories,
    meta: {
      title: 'Categories | Ceylon Craft',
      description: 'Browse all product categories including home decor, art, journals, and gifts.'
    }
  },
  {
    path: '/category/:slug',
    component: CategoryDetail,
    meta: {
      title: 'Category | Ceylon Craft',
      description: 'Browse products in this category.'
    }
  },
  {
    path: '/about',
    component: About,
    meta: {
      title: 'About Ceylon Craft | Our Story',
      description: 'Learn about Ceylon Craft and our mission to support local Sri Lankan artisans.'
    }
  },
  {
    path: '/contact',
    component: Contact,
    meta: {
      title: 'Contact Ceylon Craft | Get in Touch',
      description: 'Contact us to learn more about our handmade products and services.'
    }
  },
  {
    path: '/blog',
    component: Blog,
    meta: {
      title: 'Blog | Ceylon Craft',
      description: 'Read inspiring stories and tips about handmade products and Sri Lankan crafts.'
    }
  },
  {
    path: '/blog/:slug',
    component: BlogPost,
    meta: {
      title: 'Blog Post | Ceylon Craft',
      description: 'Read more about handmade crafts and artisan culture.'
    }
  },
  {
    path: '/login',
    component: Login,
    meta: {
      title: 'Login | Ceylon Craft',
      description: 'Sign in to your Ceylon Craft account.',
      requiresGuest: true
    }
  },
  {
    path: '/register',
    component: Register,
    meta: {
      title: 'Register | Ceylon Craft',
      description: 'Create a new Ceylon Craft account.',
      requiresGuest: true
    }
  },
  {
    path: '/cart',
    component: Cart,
    meta: {
      title: 'Shopping Cart | Ceylon Craft',
      description: 'View your shopping cart.',
      requiresAuth: true
    }
  },
  {
    path: '/checkout',
    component: Checkout,
    meta: {
      title: 'Checkout | Ceylon Craft',
      description: 'Complete your purchase.',
      requiresAuth: true
    }
  },
  {
    path: '/wishlist',
    component: Wishlist,
    meta: {
      title: 'My Wishlist | Ceylon Craft',
      description: 'View your saved favorite products.',
      requiresAuth: true
    }
  },
  {
    path: '/profile',
    component: Profile,
    meta: {
      title: 'My Profile | Ceylon Craft',
      description: 'Manage your account settings and personal information.',
      requiresAuth: true
    }
  },
  {
    path: '/orders',
    component: Orders,
    meta: {
      title: 'My Orders | Ceylon Craft',
      description: 'View your order history and tracking information.',
      requiresAuth: true
    }
  },
  {
    path: '/order/:orderId',
    component: OrderDetail,
    meta: {
      title: 'Order Details | Ceylon Craft',
      description: 'View detailed order information and tracking.',
      requiresAuth: true
    }
  },
  {
    path: '/search',
    component: SearchResults,
    meta: {
      title: 'Search Results | Ceylon Craft',
      description: 'Search results for your query.'
    }
  },
  {
    path: '/privacy-policy',
    component: PrivacyPolicy,
    meta: {
      title: 'Privacy Policy | Ceylon Craft',
      description: 'Read our privacy policy to understand how we protect your data.'
    }
  },
  {
    path: '/terms',
    component: Terms,
    meta: {
      title: 'Terms & Conditions | Ceylon Craft',
      description: 'Review our terms and conditions for using Ceylon Craft.'
    }
  },
  {
    path: '/shipping-returns',
    component: ShippingReturns,
    meta: {
      title: 'Shipping & Returns | Ceylon Craft',
      description: 'Learn about our shipping options and return policy.'
    }
  },
  {
    path: '/faq',
    component: FAQ,
    meta: {
      title: 'FAQ | Ceylon Craft',
      description: 'Frequently asked questions about Ceylon Craft products and services.'
    }
  },
  // Admin Routes
  {
    path: '/admin',
    component: AdminDashboard,
    meta: {
      title: 'Admin Dashboard | Ceylon Craft',
      description: 'Admin dashboard overview.',
      requiresAdmin: true
    }
  },
  {
    path: '/admin/dashboard',
    component: AdminDashboard,
    meta: {
      title: 'Admin Dashboard | Ceylon Craft',
      description: 'Admin dashboard overview.',
      requiresAdmin: true
    }
  },
  {
    path: '/admin/products',
    component: AdminProducts,
    meta: {
      title: 'Product Management | Ceylon Craft Admin',
      description: 'Manage products in your store.',
      requiresAdmin: true
    }
  },
  {
    path: '/admin/orders',
    component: AdminOrders,
    meta: {
      title: 'Order Management | Ceylon Craft Admin',
      description: 'Manage customer orders.',
      requiresAdmin: true
    }
  },
  {
    path: '/admin/categories',
    component: AdminCategories,
    meta: {
      title: 'Category Management | Ceylon Craft Admin',
      description: 'Manage product categories.',
      requiresAdmin: true
    }
  },
  {
    path: '/:pathMatch(.*)*',
    component: NotFound,
    meta: {
      title: 'Page Not Found | Ceylon Craft',
      description: 'The page you are looking for does not exist.'
    }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Setup route guards (auth, admin, guest protection)
setupRouteGuards(router);

export default router;
