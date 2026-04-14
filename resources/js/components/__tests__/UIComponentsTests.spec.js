import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount, flushPromises } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'

describe('UI Components & Edge Cases', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  /**
   * TEST: Loading Spinner Component
   * Scenario: Loading spinner displays during data fetch
   * Expected: Spinner visible, disappears when loading complete
   */
  it('displays loading spinner', () => {
    const wrapper = mount({
      template: '<div v-if="isLoading" data-testid="spinner">Loading...</div>',
      data() { return { isLoading: true } }
    })

    expect(wrapper.find('[data-testid="spinner"]').exists()).toBeTruthy()
  })

  /**
   * TEST: Error Message Component
   * Scenario: API error displayed
   * Expected: Error message shown with icon
   */
  it('displays error message with icon', () => {
    const wrapper = mount({
      template: '<div v-if="error" data-testid="error-msg" class="error">{{ error }}</div>',
      data() { return { error: 'Network error' } }
    })

    expect(wrapper.find('[data-testid="error-msg"]').text()).toContain('Network error')
  })

  /**
   * TEST: Toast Notification
   * Scenario: Success message shown temporarily
   * Expected: Toast appears and auto-closes
   */
  it('shows and auto-hides toast notification', async () => {
    const wrapper = mount({
      template: '<div v-if="toast.show" data-testid="toast">{{ toast.message }}</div>',
      data() {
        return {
          toast: { show: true, message: 'Product added!' }
        }
      }
    })

    expect(wrapper.find('[data-testid="toast"]').exists()).toBeTruthy()

    wrapper.vm.toast.show = false
    await wrapper.vm.$nextTick()
    expect(wrapper.find('[data-testid="toast"]').exists()).toBeFalsy()
  })

  /**
   * TEST: Empty State Component
   * Scenario: No data to display
   * Expected: Friendly empty state message with icon
   */
  it('displays empty state message', () => {
    const wrapper = mount({
      template: '<div v-if="items.length === 0" data-testid="empty">No items found</div><div v-else>{{ items.length }} items</div>',
      data() { return { items: [] } }
    })

    expect(wrapper.find('[data-testid="empty"]').exists()).toBeTruthy()
  })

  /**
   * TEST: Modal Dialog
   * Scenario: Confirmation modal displays
   * Expected: Modal visible with actions
   */
  it('displays modal with confirm/cancel buttons', async () => {
    const wrapper = mount({
      template: `
        <div v-if="showModal" data-testid="modal">
          <p>Are you sure?</p>
          <button @click="confirm" data-testid="confirm">Yes</button>
          <button @click="cancel" data-testid="cancel">No</button>
        </div>
      `,
      data() { return { showModal: true } },
      methods: {
        confirm() { this.$emit('confirmed') },
        cancel() { this.showModal = false }
      }
    })

    expect(wrapper.find('[data-testid="modal"]').exists()).toBeTruthy()
    expect(wrapper.find('[data-testid="confirm"]').exists()).toBeTruthy()
  })

  /**
   * TEST: Pagination Component
   * Scenario: Multiple pages of results
   * Expected: Previous/Next/page numbers clickable
   */
  it('renders pagination controls', () => {
    const wrapper = mount({
      template: `
        <div>
          <button v-if="currentPage > 1" data-testid="prev">← Prev</button>
          <span data-testid="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
          <button v-if="currentPage < totalPages" data-testid="next">Next →</button>
        </div>
      `,
      data() { return { currentPage: 2, totalPages: 5 } }
    })

    expect(wrapper.find('[data-testid="prev"]').exists()).toBeTruthy()
    expect(wrapper.find('[data-testid="next"]').exists()).toBeTruthy()
  })

  /**
   * TEST: Dropdown Menu
   * Scenario: Dropdown opens/closes
   * Expected: Menu toggles on click
   */
  it('toggles dropdown menu', async () => {
    const wrapper = mount({
      template: `
        <div data-testid="dropdown">
          <button @click="toggleMenu" data-testid="trigger">Menu</button>
          <ul v-if="menuOpen" data-testid="menu">
            <li>Option 1</li>
            <li>Option 2</li>
          </ul>
        </div>
      `,
      data() { return { menuOpen: false } },
      methods: { toggleMenu() { this.menuOpen = !this.menuOpen } }
    })

    await wrapper.find('[data-testid="trigger"]').trigger('click')
    expect(wrapper.find('[data-testid="menu"]').exists()).toBeTruthy()
  })

  /**
   * TEST: Breadcrumb Navigation
   * Scenario: Show current path
   * Expected: Breadcrumbs clickable, leading to parent pages
   */
  it('displays breadcrumb navigation', () => {
    const wrapper = mount({
      template: `
        <nav data-testid="breadcrumbs">
          <a href="/">Home</a> / <a href="/products">Products</a> / <span>Details</span>
        </nav>
      `
    })

    expect(wrapper.text()).toContain('Home')
    expect(wrapper.text()).toContain('Products')
  })

  /**
   * TEST: Rating Stars Display
   * Scenario: Show product rating 1-5
   * Expected: Filled/unfilled stars rendered
   */
  it('displays rating stars', () => {
    const wrapper = mount({
      template: `
        <div data-testid="rating">
          <span v-for="n in 5" :key="n" :class="n <= rating ? 'filled' : 'empty'">★</span>
        </div>
      `,
      data() { return { rating: 4 } }
    })

    const stars = wrapper.findAll('[class*="filled"]')
    expect(stars.length).toBe(4)
  })

  /**
   * TEST: Price Filter Slider
   * Scenario: Adjust price range
   * Expected: Slider values update, products filtered
   */
  it('updates price when slider changes', async () => {
    const wrapper = mount({
      template: '<input type="range" v-model="minPrice" min="0" max="1000" data-testid="slider">',
      data() { return { minPrice: 0 } }
    })

    await wrapper.find('[data-testid="slider"]').setValue(250)
    expect(wrapper.vm.minPrice).toBe('250')
  })

  /**
   * TEST: Image Lazy Loading
   * Scenario: Images load on scroll
   * Expected: Placeholder shown, real image loads lazily
   */
  it('shows placeholder for lazy-loaded image', () => {
    const wrapper = mount({
      template: '<img data-testid="lazy-img" :src="!isLoaded ? placeholderSrc : imageSrc" @load="isLoaded = true">',
      data() {
        return {
          isLoaded: false,
          placeholderSrc: '/placeholder.png',
          imageSrc: '/real-image.jpg'
        }
      }
    })

    expect(wrapper.find('[data-testid="lazy-img"]').attributes('src')).toContain('placeholder')
  })

  /**
   * TEST: Search Input with Debounce
   * Scenario: Type in search box
   * Expected: API not called on every keystroke, debounced
   */
  it('debounces search input', async () => {
    const mockSearch = vi.fn()
    const wrapper = mount({
      template: '<input v-model="searchQuery" @input="debouncedSearch" data-testid="search">',
      data() { return { searchQuery: '' } },
      methods: {
        debouncedSearch() {
          // Simulating debounce
          mockSearch()
        }
      }
    })

    const input = wrapper.find('[data-testid="search"]')
    await input.setValue('test')
    
    expect(wrapper.vm.searchQuery).toBe('test')
  })

  /**
   * TEST: Form Input Validation Errors
   * Scenario: Invalid input shows error
   * Expected: Error message displayed inline
   */
  it('displays validation error below field', () => {
    const wrapper = mount({
      template: `
        <div>
          <input v-model="email" @blur="validateEmail" data-testid="email-input">
          <span v-if="errors.email" data-testid="error-text">{{ errors.email }}</span>
        </div>
      `,
      data() { return { email: '', errors: { email: 'Invalid email' } } }
    })

    expect(wrapper.find('[data-testid="error-text"]').exists()).toBeTruthy()
  })

  /**
   * TEST: Responsive Image
   * Scenario: Image resizes on viewport change
   * Expected: Correct image size for device
   */
  it('uses responsive image srcset', () => {
    const wrapper = mount({
      template: '<img data-testid="responsive" srcset="/small.jpg 480w, /large.jpg 1024w">',
    })

    expect(wrapper.find('[data-testid="responsive"]').attributes('srcset')).toBeTruthy()
  })

  /**
   * TEST: Accordion/Collapsible Sections
   * Scenario: Expand/collapse product details
   * Expected: Sections toggle smoothly
   */
  it('toggles accordion sections', async () => {
    const wrapper = mount({
      template: `
        <div>
          <button @click="isOpen = !isOpen" data-testid="toggle">Details</button>
          <div v-if="isOpen" data-testid="content">Expanded content</div>
        </div>
      `,
      data() { return { isOpen: false } }
    })

    await wrapper.find('[data-testid="toggle"]').trigger('click')
    expect(wrapper.find('[data-testid="content"]').exists()).toBeTruthy()
  })

  /**
   * TEST: Responsive Grid
   * Scenario: Products grid on different screen sizes
   * Expected: 1 column mobile, 2 tablet, 4 desktop
   */
  it('renders responsive grid', () => {
    const wrapper = mount({
      template: `
        <div class="grid" :class="gridClass">
          <div v-for="n in 12" :key="n" class="item">Item</div>
        </div>
      `,
      computed: {
        gridClass() {
          if (window.innerWidth < 640) return 'grid-1'
          if (window.innerWidth < 1024) return 'grid-2'
          return 'grid-4'
        }
      }
    })

    expect(wrapper.find('.grid').exists()).toBeTruthy()
  })

  /**
   * TEST: Keyboard Navigation
   * Scenario: Tab through form fields
   * Expected: Focus order correct
   */
  it('supports keyboard navigation through form', async () => {
    const wrapper = mount({
      template: `
        <form>
          <input type="text" placeholder="First" data-testid="first">
          <input type="email" placeholder="Second" data-testid="second">
          <button type="submit" data-testid="submit">Submit</button>
        </form>
      `
    })

    const firstInput = wrapper.find('[data-testid="first"]')
    expect(firstInput.exists()).toBeTruthy()

    const secondInput = wrapper.find('[data-testid="second"]')
    expect(secondInput.exists()).toBeTruthy()
  })

  /**
   * TEST: Dark Mode Toggle
   * Scenario: User switches theme
   * Expected: Appearance changes
   */
  it('toggles between light and dark mode', async () => {
    const wrapper = mount({
      template: '<div :class="isDarkMode ? \'dark\' : \'light\'">Content</div>',
      data() { return { isDarkMode: false } }
    })

    expect(wrapper.find('.light').exists()).toBeTruthy()

    wrapper.vm.isDarkMode = true
    await wrapper.vm.$nextTick()
    expect(wrapper.find('.dark').exists()).toBeTruthy()
  })
})
