document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".view-product").forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      const productId = this.getAttribute("data-id");
      showProductModal(productId);
    });
  });
});

async function showProductModal(productId) {
  try {
    const loadingModal = createLoadingModal();
    document.body.appendChild(loadingModal);

    const response = await fetch(`get_product_details.php?id=${productId}`);
    if (!response.ok) throw new Error("Network response was not ok");

    const product = await response.json();
    if (product.error) throw new Error(product.error);

    loadingModal.remove();
    displayProductModal(product);
  } catch (error) {
    console.error("Error:", error);
    showErrorModal(error.message || "Failed to load product details");
  }
}

function displayProductModal(product) {
  const modal = document.createElement("div");
  modal.className =
    "fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4";
  modal.innerHTML = `
      <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col modal-container">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-4 border-b border-gray-200 sticky-modal-header bg-white">
          <h2 class="text-xl font-bold text-gray-800">${escapeHtml(
            product.name
          )}</h2>
          <button class="text-gray-500 hover:text-gray-700 close-modal">
            <i class="fas fa-times text-xl"></i>
          </button>
        </div>
        
        <!-- Scrollable Content Area -->
        <div class="overflow-y-auto flex-1 p-4 modal-content">
          <!-- Primary Images (4 images) -->
          <div class="grid grid-cols-2 gap-4 mb-6">
            ${
              product.pImage1
                ? createImageCard(product.pImage1, product.name, "h-64")
                : ""
            }
            ${
              product.pImage2
                ? createImageCard(product.pImage2, product.name, "h-64")
                : ""
            }
            ${
              product.pImage3
                ? createImageCard(product.pImage3, product.name, "h-64")
                : ""
            }
            ${
              product.pImage4
                ? createImageCard(product.pImage4, product.name, "h-64")
                : ""
            }
          </div>
          
          <!-- Product Description -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Description</h3>
            <p class="text-gray-700 mb-6 whitespace-pre-line">${escapeHtml(
              product.description
            )}</p>
            
            <!-- Additional Media -->
            <h3 class="text-lg font-semibold mb-3">Additional Media</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
              ${
                product.rImage1
                  ? createImageCard(
                      product.rImage1,
                      "Additional image 1",
                      "h-32"
                    )
                  : ""
              }
              ${
                product.rImage2
                  ? createImageCard(
                      product.rImage2,
                      "Additional image 2",
                      "h-32"
                    )
                  : ""
              }
              ${
                product.rImage3
                  ? createImageCard(
                      product.rImage3,
                      "Additional image 3",
                      "h-32"
                    )
                  : ""
              }
              ${product.rVideo ? createVideoCard(product.rVideo) : ""}
            </div>
          </div>
          
          <!-- Product Details -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 class="text-lg font-semibold mb-2">Materials</h3>
              <p class="text-gray-700">${
                escapeHtml(product.materials) || "N/A"
              }</p>
            </div>
            <div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <h4 class="text-sm font-medium text-gray-500">Price</h4>
                  <p class="text-lg font-bold">ETB ${parseFloat(
                    product.price
                  ).toFixed(2)}</p>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-500">Quantity</h4>
                  <p class="text-lg ${
                    product.quantity > 0 ? "text-green-600" : "text-red-600"
                  }">
                    ${product.quantity > 0 ? product.quantity : "Out of Stock"}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex justify-end space-x-3 p-4 border-t border-gray-200 sticky-modal-footer bg-white">
          <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 close-modal">
            Close
          </button>
          <a href="edit_product.php?id=${
            product.id
          }" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-edit mr-2"></i> Edit
          </a>
        </div>
      </div>
    `;

  modal.querySelectorAll(".close-modal").forEach((btn) => {
    btn.addEventListener("click", () => modal.remove());
  });

  modal.addEventListener("click", (e) => {
    if (e.target === modal) modal.remove();
  });

  document.addEventListener(
    "keydown",
    function handleEscape(e) {
      if (e.key === "Escape") modal.remove();
    },
    {
      once: true,
    }
  );

  document.body.appendChild(modal);
}

function createImageCard(image, alt, heightClass = "h-48") {
  return `
      <div class="bg-gray-100 rounded-lg overflow-hidden">
        <img src="../uploads/products/${escapeHtml(image)}" 
           alt="${escapeHtml(alt)}" 
           class="w-full ${heightClass} object-contain">
      </div>
    `;
}

function createVideoCard(videoFilename) {
  if (!videoFilename) return "";

  return `
      <div class="bg-gray-100 rounded-lg overflow-hidden relative h-32">
        <div class="video-container">
          <video controls class="w-full h-full">
            <source src="../uploads/products/${escapeHtml(
              videoFilename
            )}" type="video/mp4">
            Your browser does not support HTML5 video.
          </video>
        </div>
        <div class="absolute bottom-2 left-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
          <i class="fas fa-play mr-1"></i> Video
        </div>
      </div>
    `;
}

function createLoadingModal() {
  const modal = document.createElement("div");
  modal.className =
    "fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50";
  modal.innerHTML = `
      <div class="bg-white p-6 rounded-lg shadow-xl">
        <div class="flex items-center">
          <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>Loading product details...</span>
        </div>
      </div>
    `;
  return modal;
}

function showErrorModal(message) {
  const errorModal = document.createElement("div");
  errorModal.className =
    "fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50";
  errorModal.innerHTML = `
      <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
        <div class="text-red-500 mb-4">
          <i class="fas fa-exclamation-circle text-2xl"></i>
          <h3 class="text-xl font-bold inline-block ml-2">Error Loading Product</h3>
        </div>
        <p class="mb-4">${escapeHtml(message)}</p>
        <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 float-right close-error-modal">
          OK
        </button>
      </div>
    `;
  errorModal
    .querySelector(".close-error-modal")
    .addEventListener("click", () => {
      errorModal.remove();
    });
  document.body.appendChild(errorModal);
}

function escapeHtml(unsafe) {
  if (!unsafe) return "";
  return unsafe
    .toString()
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}
