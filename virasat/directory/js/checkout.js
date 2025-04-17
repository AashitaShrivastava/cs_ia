function placeOrder() {
    alert("Order placed successfully!");

    // Clear the cart after checkout
    localStorage.removeItem("cart");

    // Redirect to a confirmation page (or homepage)
    window.location.href = "confirmation.html";
}
document.getElementById("checkout-btn").addEventListener("click", function () {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    if (cart.length === 0) {
        alert("Your cart is empty.");
        return;
    }

    fetch("place_order.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "cart=" + encodeURIComponent(JSON.stringify(cart)),
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert("Order placed successfully! Order ID: " + data.order_id);
            localStorage.removeItem("cart"); // Clear cart
            window.location.href = "order_confirmation.php?order_id=" + data.order_id;
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
});
