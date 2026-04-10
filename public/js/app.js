const cartCountEl = document.getElementById("cart-count");

function setCartCount(count) {
  if (cartCountEl) {
    cartCountEl.textContent = String(count);
  }
}

async function postJson(url, payload) {
  const response = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });

  return response.json();
}

function formatPrice(paise) {
  const rupees = paise / 100;
  return `₹${rupees.toLocaleString("en-IN", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

document.querySelectorAll(".js-add-cart").forEach((button) => {
  button.addEventListener("click", async () => {
    const productId = button.dataset.productId;
    const data = await postJson("/cart/add", { product_id: productId, qty: 1 });
    if (data.success) {
      setCartCount(data.cart_count);
    }
  });
});

let detailQty = 1;
const detailQtyEl = document.getElementById("detail-qty");
if (detailQtyEl) {
  document.querySelectorAll("[data-qty-action]").forEach((button) => {
    button.addEventListener("click", () => {
      if (button.dataset.qtyAction === "plus") {
        detailQty += 1;
      } else {
        detailQty = Math.max(1, detailQty - 1);
      }
      detailQtyEl.textContent = String(detailQty);
    });
  });

  const addDetailButton = document.querySelector(".js-add-cart-detail");
  if (addDetailButton) {
    addDetailButton.addEventListener("click", async () => {
      const productId = addDetailButton.dataset.productId;
      const data = await postJson("/cart/add", {
        product_id: productId,
        qty: detailQty,
      });
      if (data.success) {
        setCartCount(data.cart_count);
      }
    });
  }
}

function renderCartItems(items) {
  const cartItems = document.getElementById("cart-items");
  if (!cartItems) {
    return;
  }

  if (items.length === 0) {
    cartItems.innerHTML = '<p class="muted">Your cart is empty.</p>';
    return;
  }

  cartItems.innerHTML = items
    .map(
      (item) => `
        <article class="cart-item" data-product-id="${item.product_id}">
            <div class="image-box small"><img class="product-image" src="${item.image_url || "/images/products/placeholder.svg"}" alt="${item.name}"></div>
            <div>
                <h3>${item.name}</h3>
                <p class="muted">${item.unit}</p>
            </div>
            <div class="cart-actions">
                <button class="qty-btn js-cart-remove" type="button" data-product-id="${item.product_id}">−</button>
                <span>${item.qty}</span>
                <button class="qty-btn js-cart-add" type="button" data-product-id="${item.product_id}">+</button>
            </div>
            <strong>${formatPrice(item.price * item.qty)}</strong>
        </article>
    `,
    )
    .join("");

  bindCartButtons();
}

async function updateCart(productId, qty, endpoint) {
  const data = await postJson(endpoint, { product_id: productId, qty });
  if (!data.success) {
    return;
  }

  setCartCount(data.cart_count);
  renderCartItems(data.items || []);

  const subtotalEl = document.getElementById("subtotal-value");
  const deliveryEl = document.getElementById("delivery-value");
  const totalEl = document.getElementById("total-value");
  if (subtotalEl) subtotalEl.textContent = data.subtotal_formatted;
  if (deliveryEl) deliveryEl.textContent = data.delivery_fee_formatted;
  if (totalEl) totalEl.textContent = data.total_formatted;
}

function bindCartButtons() {
  document.querySelectorAll(".js-cart-add").forEach((button) => {
    button.addEventListener("click", () => {
      updateCart(button.dataset.productId, 1, "/cart/add");
    });
  });

  document.querySelectorAll(".js-cart-remove").forEach((button) => {
    button.addEventListener("click", () => {
      updateCart(button.dataset.productId, 1, "/cart/remove");
    });
  });
}

bindCartButtons();

const searchInput = document.getElementById("search-input");
const categoryButtons = document.querySelectorAll("#category-filters .pill");
const productCards = document.querySelectorAll("#product-grid .product-card");
let activeCategory = "all";

function applyFilters() {
  const keyword = (searchInput?.value || "").toLowerCase().trim();

  productCards.forEach((card) => {
    const cardCategory = card.dataset.category;
    const cardName = card.dataset.name;

    const categoryMatch =
      activeCategory === "all" || cardCategory === activeCategory;
    const searchMatch = cardName.includes(keyword);

    card.style.display = categoryMatch && searchMatch ? "block" : "none";
  });
}

if (searchInput) {
  searchInput.addEventListener("input", applyFilters);
}

categoryButtons.forEach((button) => {
  button.addEventListener("click", () => {
    categoryButtons.forEach((item) => item.classList.remove("active"));
    button.classList.add("active");
    activeCategory = button.dataset.category;
    applyFilters();
  });
});

const productModal = document.getElementById("product-modal");
const modalQtyEl = document.getElementById("modal-qty");
const modalQtyMinus = document.getElementById("modal-qty-minus");
const modalQtyPlus = document.getElementById("modal-qty-plus");
const modalAddCart = document.getElementById("modal-add-cart");
const modalImage = document.getElementById("modal-product-image");
const modalTitle = document.getElementById("product-modal-title");
const modalUnit = document.getElementById("modal-product-unit");
const modalDescription = document.getElementById("modal-product-description");
const modalPrice = document.getElementById("modal-product-price");
let modalQty = 1;

function closeProductModal() {
  if (!productModal) return;
  productModal.classList.remove("open");
  productModal.setAttribute("aria-hidden", "true");
  document.body.style.overflow = "";
}

function openProductModal(card) {
  if (!productModal || !modalAddCart) return;

  modalQty = 1;
  if (modalQtyEl) {
    modalQtyEl.textContent = "1";
  }

  modalAddCart.dataset.productId = card.dataset.productId || "";
  if (modalImage) {
    modalImage.src = card.dataset.productImage || "/images/products/placeholder.svg";
    modalImage.alt = card.dataset.productTitle || "Product image";
  }
  if (modalTitle) {
    modalTitle.textContent = card.dataset.productTitle || "Product";
  }
  if (modalUnit) {
    modalUnit.textContent = card.dataset.productUnit || "";
  }
  if (modalDescription) {
    modalDescription.textContent = card.dataset.productDescription || "";
  }
  if (modalPrice) {
    modalPrice.textContent = formatPrice(Number(card.dataset.productPrice || 0));
  }

  productModal.classList.add("open");
  productModal.setAttribute("aria-hidden", "false");
  document.body.style.overflow = "hidden";
}

document.querySelectorAll(".js-product-trigger").forEach((trigger) => {
  trigger.addEventListener("click", (event) => {
    event.preventDefault();
    const card = trigger.closest(".product-card");
    if (card) {
      openProductModal(card);
    }
  });
});

if (modalQtyMinus && modalQtyEl) {
  modalQtyMinus.addEventListener("click", () => {
    modalQty = Math.max(1, modalQty - 1);
    modalQtyEl.textContent = String(modalQty);
  });
}

if (modalQtyPlus && modalQtyEl) {
  modalQtyPlus.addEventListener("click", () => {
    modalQty += 1;
    modalQtyEl.textContent = String(modalQty);
  });
}

if (modalAddCart) {
  modalAddCart.addEventListener("click", async () => {
    const productId = modalAddCart.dataset.productId;
    if (!productId) return;

    const data = await postJson("/cart/add", { product_id: productId, qty: modalQty });
    if (data.success) {
      setCartCount(data.cart_count);
      closeProductModal();
    }
  });
}

if (productModal) {
  productModal.addEventListener("click", (event) => {
    const target = event.target;
    if (target && target instanceof HTMLElement && target.hasAttribute("data-modal-close")) {
      closeProductModal();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeProductModal();
    }
  });
}

document.querySelectorAll(".js-order-status").forEach((select) => {
  select.addEventListener("change", async () => {
    await postJson("/admin/orders/status", {
      order_id: select.dataset.orderId,
      status: select.value,
    });
  });
});

const hamburger = document.getElementById("hamburger");
const navMenu = document.getElementById("navMenu");
if (hamburger && navMenu) {
  hamburger.addEventListener("click", () => {
    navMenu.classList.toggle("open");
  });
}

function urlBase64ToUint8Array(base64String) {
  const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
  const base64 = (base64String + padding).replace(/-/g, "+").replace(/_/g, "/");
  const rawData = window.atob(base64);
  const outputArray = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }

  return outputArray;
}

function showAdminToast(message) {
  const stack = document.getElementById("admin-notification-stack");
  if (!stack) return;

  const toast = document.createElement("div");
  toast.className = "admin-toast";
  toast.textContent = message;
  stack.prepend(toast);

  setTimeout(() => {
    toast.remove();
  }, 6000);
}

function initAdminNotifications() {
  const body = document.body;
  if (!body || body.dataset.adminDashboard !== "1") {
    return;
  }

  let latestOrderMs = Number(body.dataset.latestOrderMs || 0);
  const vapidPublicKey = body.dataset.vapidPublicKey || "";

  async function pollNewOrders() {
    try {
      const response = await fetch(
        `/admin/orders/poll?since=${latestOrderMs}`,
        {
          credentials: "same-origin",
        },
      );

      const data = await response.json();
      if (!data.success) return;

      (data.orders || []).forEach((order) => {
        const text = `New order from ${order.customer_name} (${formatPrice(order.total)})`;
        showAdminToast(text);

        if (
          document.visibilityState !== "visible" &&
          "Notification" in window &&
          Notification.permission === "granted"
        ) {
          new Notification("New customer order", {
            body: `${order.customer_name} placed an order of ${formatPrice(order.total)}`,
          });
        }
      });

      latestOrderMs = Math.max(
        latestOrderMs,
        Number(data.latest_order_ms || 0),
      );
    } catch (error) {
      // Ignore polling errors and retry on next interval.
    }
  }

  async function enablePushNotifications() {
    if (!("serviceWorker" in navigator) || !("PushManager" in window)) {
      showAdminToast("Push notifications are not supported in this browser.");
      return;
    }

    if (!vapidPublicKey) {
      showAdminToast("Push notifications are not configured yet.");
      return;
    }

    const permission =
      Notification.permission === "granted"
        ? "granted"
        : await Notification.requestPermission();

    if (permission !== "granted") {
      showAdminToast("Notification permission was not granted.");
      return;
    }

    const registration = await navigator.serviceWorker.register("/sw.js");
    const existing = await registration.pushManager.getSubscription();
    const subscription =
      existing ||
      (await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
      }));

    const response = await fetch("/admin/notifications/subscribe", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "same-origin",
      body: JSON.stringify(subscription.toJSON()),
    });

    const data = await response.json();
    if (data.success) {
      showAdminToast("Push notifications enabled.");
    }
  }

  const enablePushButton = document.getElementById("enable-push");
  if (enablePushButton) {
    enablePushButton.addEventListener("click", async () => {
      try {
        await enablePushNotifications();
      } catch (error) {
        showAdminToast("Could not enable push notifications.");
      }
    });
  }

  setInterval(pollNewOrders, 10000);
  pollNewOrders();
}

initAdminNotifications();
