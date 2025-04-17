// Mock Data for Products
const products = [
    { id: 1, name: "Hand-painted Canvas", price: 1200 },
    { id: 2, name: "Embroidered Dress", price: 1800 },
    { id: 3, name: "Tribal Print Saree", price: 1500 },
    { id: 4, name: "Lotus Dance Shawl", price: 900 },
];

// Initialize Cart from Local Storage
let cart = JSON.parse(localStorage.getItem('cart')) || [];

// Display Products
function displayProducts() {
    const productList = document.getElementById("product-list");
    products.forEach(product => {
        const productDiv = document.createElement("div");
        productDiv.className = "product-item";
        productDiv.innerHTML = `
            <h3>${product.name}</h3>
            <p>Price: $${product.price}</p>
            <button onclick="addToCart(${product.id})">Add to Cart</button>
        `;
        productList.appendChild(productDiv);
    });
}
function displayCart() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let cartContainer = document.getElementById("cart-items"); // Make sure this ID exists

    if (!cartContainer) {
        console.error("Cart container not found!"); // Debugging
        return;
    }

    cartContainer.innerHTML = ""; // Clear previous cart items

    if (cart.length === 0) {
        cartContainer.innerHTML = "<p>Your cart is empty.</p>";
        return;
    }

    let totalPrice = 0;
    let totalItems = 0;

    cart.forEach((item, index) => {
        totalItems += item.quantity;
        totalPrice += item.price * item.quantity;

        let cartItem = document.createElement("div");
        cartItem.classList.add("cart-item");
        cartItem.innerHTML = `
            <img src="${item.image}" alt="${item.name}" style="width: 50px;">
            <h3>${item.name}</h3>
            <p>Price: ₹${item.price}</p>
            <p>Quantity: ${item.quantity}</p>
            <button onclick="removeFromCart(${index})">Remove</button>
        `;
        cartContainer.appendChild(cartItem);
    });

    document.getElementById("cart-total").innerText = `Subtotal: ₹${totalPrice.toFixed(2)}`;
    document.getElementById("cart-count").innerText = `Total Items: ${totalItems}`;
}

// Add Product to Cart
function addToCart(id) {
    const product = products.find(p => p.id === id);
    if (!product) return;

    const existingProductIndex = cart.findIndex(item => item.name === product.name);

    if (existingProductIndex !== -1) {
        cart[existingProductIndex].quantity += 1;
    } else {
        cart.push({ name: product.name, price: product.price, quantity: 1 });
    }

    localStorage.setItem('cart', JSON.stringify(cart));

    // Show the modal with the product name
    showModal(`${product.name} has been added to your cart!`);
}

// Show Modal Pop-up
function showModal(message) {
    const modal = document.getElementById("cartModal");
    const modalText = document.getElementById("modalText");
    modalText.innerText = message;
    modal.style.display = "block";

    // Auto-close modal after 2 seconds
    setTimeout(() => {
        closeModal();
    }, 2000);
}

// Close Modal Pop-up
function closeModal() {
    const modal = document.getElementById("cartModal");
    modal.style.display = "none";
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById("cartModal");
    if (event.target === modal) {
        closeModal();
    }
}

// Initialize
document.addEventListener("DOMContentLoaded", displayProducts);

document.addEventListener("DOMContentLoaded", function() {
    checkLoginStatus();

    // Handle Login
    document.getElementById("login-form").addEventListener("submit", function(event) {
        event.preventDefault();

        let email = document.getElementById("email").value;
        let password = document.getElementById("password").value;
        let rememberMe = document.getElementById("remember-me").checked;

        let users = JSON.parse(localStorage.getItem("users")) || [];
        let validUser = users.find(user => user.email === email && user.password === btoa(password));

        if (validUser) {
            if (rememberMe) {
                localStorage.setItem("loggedInUser", JSON.stringify(validUser));
            } else {
                sessionStorage.setItem("loggedInUser", JSON.stringify(validUser)); // Temporary login
            }
            
            closeModal();
            setTimeout(() => {
                location.reload(); // Refresh page to ensure modal disappears
            }, 500);
        } else {
            alert("Invalid email or password.");
        }
    });
});

// Check if user is logged in
function checkLoginStatus() {
    let user = JSON.parse(localStorage.getItem("loggedInUser")) || JSON.parse(sessionStorage.getItem("loggedInUser"));
    if (!user) {
        document.getElementById("login-modal").style.display = "flex";
    } else {
        document.getElementById("login-modal").style.display = "none";
    }
}

// Close modal after login
function closeModal() {
    document.getElementById("login-modal").style.display = "none";
}
