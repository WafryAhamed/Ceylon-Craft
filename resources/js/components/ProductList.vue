<template>
  <div class="products-page">
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <h1>Handmade with Heart</h1>
        <p>Discover unique handcrafted products from artisans</p>
      </div>
    </section>

    <!-- Product Grid Section -->
    <section class="products-section">
      <div class="container">
        <h2>Our Products</h2>
        
        <div class="products-grid">
          <div v-if="products.length" class="product-list">
            <div v-for="product in products" :key="product.id" class="product-card">
              <div class="product-image">
                <img :src="product.image" :alt="product.name">
              </div>
              <div class="product-details">
                <h3>{{ product.name }}</h3>
                <p class="price">${{ Number(product.price).toFixed(2) }}</p>
              </div>
            </div>
          </div>
          <div v-else class="loading">
            <p>Loading products...</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'ProductList',
  data() {
    return {
      products: [],
      loading: true,
    };
  },
  mounted() {
    this.fetchProducts();
  },
  methods: {
    fetchProducts() {
      fetch('/api/products')
        .then(response => response.json())
        .then(data => {
          this.products = data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Error fetching products:', error);
          this.loading = false;
        });
    },
  },
};
</script>

<style scoped>
.products-page {
  width: 100%;
}

/* Hero Section */
.hero {
  background: linear-gradient(135deg, #f5f1eb 0%, #ffffff 100%);
  padding: 80px 20px;
  text-align: center;
  border-radius: 0;
}

.hero-content h1 {
  font-size: 48px;
  color: #1A1A1A;
  margin-bottom: 15px;
  font-weight: 600;
  letter-spacing: -0.5px;
}

.hero-content p {
  font-size: 18px;
  color: #666;
  font-weight: 400;
}

/* Products Section */
.products-section {
  padding: 60px 20px;
  background: #FFFFFF;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.products-section h2 {
  font-size: 36px;
  color: #1A1A1A;
  margin-bottom: 40px;
  text-align: center;
  font-weight: 600;
  letter-spacing: -0.5px;
}

/* Products Grid */
.products-grid {
  width: 100%;
}

.product-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 30px;
  width: 100%;
}

/* Product Card */
.product-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  border: 1px solid #F5F1EB;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
  border-color: #8B6F47;
}

.product-image {
  width: 100%;
  height: 280px;
  background: linear-gradient(135deg, #f5f1eb 0%, #ffffff 100%);
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  border-bottom: 1px solid #F5F1EB;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
  transform: scale(1.05);
}

.product-details {
  padding: 20px;
  background: white;
}

.product-details h3 {
  font-size: 18px;
  color: #1A1A1A;
  margin-bottom: 10px;
  font-weight: 600;
  letter-spacing: -0.3px;
}

.price {
  font-size: 20px;
  color: #8B6F47;
  font-weight: 700;
  letter-spacing: -0.3px;
}

.loading {
  text-align: center;
  padding: 80px 20px;
  color: #999;
  font-size: 16px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero-content h1 {
    font-size: 32px;
  }

  .hero {
    padding: 50px 20px;
  }

  .products-section {
    padding: 40px 20px;
  }

  .products-section h2 {
    font-size: 28px;
    margin-bottom: 30px;
  }

  .product-list {
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
  }

  .product-image {
    height: 220px;
  }
}

@media (max-width: 480px) {
  .hero-content h1 {
    font-size: 24px;
  }

  .hero-content p {
    font-size: 14px;
  }

  .products-section h2 {
    font-size: 22px;
  }

  .product-list {
    grid-template-columns: 1fr;
    gap: 15px;
  }

  .product-image {
    height: 250px;
  }

  .product-details {
    padding: 15px;
  }

  .product-details h3 {
    font-size: 16px;
  }

  .price {
    font-size: 18px;
  }
}
</style>
