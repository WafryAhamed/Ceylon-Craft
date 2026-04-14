import { createRouter, createWebHistory } from 'vue-router';

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
      description: 'Sign in to your Ceylon Craft account.'
    }
  },
  {
    path: '/register',
    component: Register,
    meta: {
      title: 'Register | Ceylon Craft',
      description: 'Create a new Ceylon Craft account.'
    }
  },
  {
    path: '/cart',
    component: Cart,
    meta: {
      title: 'Shopping Cart | Ceylon Craft',
      description: 'View your shopping cart.'
    }
  },
  {
    path: '/checkout',
    component: Checkout,
    meta: {
      title: 'Checkout | Ceylon Craft',
      description: 'Complete your purchase.'
    }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Update document title on route change
router.beforeEach((to, from, next) => {
  document.title = to.meta.title || 'Ceylon Craft';
  next();
});

export default router;
