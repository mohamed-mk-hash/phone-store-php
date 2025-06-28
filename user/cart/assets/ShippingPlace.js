
document.getElementById('city-select').addEventListener('change', function () {
    var selectedOption = this.options[this.selectedIndex];
    var shippingPrice = selectedOption.getAttribute('data-price'); // Get the shipping price
    var cartTotal = parseFloat(document.getElementById('cart-total').textContent.replace('$', '')); // Get the cart total
    var finalTotalElement = document.getElementById('final-total');

    // If a city is selected, update the shipping price and calculate the total
    if (shippingPrice) {
        shippingPrice = parseFloat(shippingPrice).toFixed(2);
        document.getElementById('shipping-price').textContent =  shippingPrice + ' DZD';

        // Calculate the final total (cart total + shipping price)
        var finalTotal = cartTotal + parseFloat(shippingPrice);
        finalTotalElement.textContent =  finalTotal.toFixed(2) + ' DZD';
    } else {
        // If no city is selected, reset the shipping price and total
        document.getElementById('shipping-price').textContent = 'DZD 0.00'; // Set default shipping price
        finalTotalElement.textContent = 'DZD ' + cartTotal.toFixed(2); // Reset final total
    }
});





    
