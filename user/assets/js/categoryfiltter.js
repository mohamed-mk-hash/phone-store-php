document.addEventListener('DOMContentLoaded', function() {
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    const allProductsCheckbox = document.querySelector('.all-products-checkbox');
    const minPriceInput = document.getElementById('min-price');
    const minPriceDisplay = document.getElementById('min-price-display');
    const goButton = document.getElementById('go-button');

        function updateFilters() {
        const selectedCategories = [];
        document.querySelectorAll('.category-checkbox:checked').forEach(function(checkbox) {
            selectedCategories.push(checkbox.id.replace('category-', ''));
        });

        const allProductsChecked = allProductsCheckbox.checked ? 1 : 0;
        const urlParams = new URLSearchParams(window.location.search);

                if (selectedCategories.length > 0 || allProductsChecked === 1) {
            urlParams.set('page', 1);
        }

                if (allProductsChecked) {
            urlParams.delete('category_id');
        } else if (selectedCategories.length > 0) {
            urlParams.set('category_id', selectedCategories.join(','));
        } else {
            urlParams.delete('category_id');
        }

                urlParams.set('all_products', allProductsChecked);

                urlParams.set('min_price', minPriceInput.value);

                window.location.search = urlParams.toString();
    }

        categoryCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                allProductsCheckbox.checked = false;
            }

            categoryCheckboxes.forEach(function(otherCheckbox) {
                if (otherCheckbox !== checkbox && otherCheckbox.checked) {
                    otherCheckbox.checked = false;
                }
            });

            updateFilters();
        });
    });

        allProductsCheckbox.addEventListener('change', function() {
        if (this.checked) {
            categoryCheckboxes.forEach(function(checkbox) {
                checkbox.checked = false;
            });
        }
        updateFilters();
    });

        if (minPriceInput) {
        minPriceInput.addEventListener('input', function() {
            minPriceDisplay.textContent = `DZD${this.value}`;
        });

                const urlParams = new URLSearchParams(window.location.search);
        minPriceInput.value = urlParams.get('min_price') || 0;        
         minPriceDisplay.textContent = `DZD${minPriceInput.value}`;
    }

        goButton.addEventListener('click', function() {
        updateFilters();      });
});