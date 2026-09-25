$(document).ready(function() {
    // Datos de ejemplo (categorías y platos)
    const categories = [
        { id: 1, name: "Entradas", img: "https://images.unsplash.com/photo-1544025162-d76694265947" },
        { id: 2, name: "Ensaladas", img: "https://images.unsplash.com/photo-1546793665-c74683f339c1" },
        { id: 3, name: "Sopas", img: "https://images.unsplash.com/photo-1476718406336-bb5a9690ee2a" },
        { id: 4, name: "Pasta", img: "https://images.unsplash.com/photo-1555949258-eb67b1ef0ceb" },
        { id: 5, name: "Carnes", img: "https://images.unsplash.com/photo-1558030006-450675393462" },
        { id: 6, name: "Pescados", img: "https://images.unsplash.com/photo-1561154464-82e9adf32764" },
        { id: 7, name: "Postres", img: "https://images.unsplash.com/photo-1563805042-7684c019e1cb" },
        { id: 8, name: "Bebidas", img: "https://images.unsplash.com/photo-1551024506-0bccd828d307" },
        { id: 9, name: "Especiales", img: "https://images.unsplash.com/photo-1565299624946-b28f40a0ae38" }
    ];

    const items = {
        1: [
            { id: 101, name: "Nachos", price: 85, img: "https://images.unsplash.com/photo-1571407970349-bc81e7e96d47" },
            { id: 102, name: "Alitas", price: 120, img: "https://images.unsplash.com/photo-1561758033-d89a9ad46330" },
            { id: 103, name: "Quesadilla", price: 95, img: "https://images.unsplash.com/photo-1615870216519-2f9fa575fa5c" }
        ],
        2: [
            { id: 201, name: "César", price: 90, img: "https://images.unsplash.com/photo-1546793665-c74683f339c1" },
            { id: 202, name: "Griega", price: 110, img: "https://images.unsplash.com/photo-1546069901-ba9599a7e63c" },
            { id: 203, name: "Caprese", price: 100, img: "https://images.unsplash.com/photo-1551248429-40975aa4de74" }
        ],
        // ... Agrega más platos para cada categoría
    };

    // Variables del ticket
    let ticketItems = [];
    let subtotal = 0;
    let taxRate = 0.16;
    let discount = 0;

    // Cargar categorías
    function loadCategories() {
        const $container = $('#categoriesContainer');
        $container.empty();
        
        categories.forEach(category => {
            $container.append(`
                <div class="col-md-4">
                    <div class="category-card" data-id="${category.id}">
                        <img src="${category.img}" class="category-img w-100">
                        <div class="card-footer text-center bg-primary text-white">
                            <h5>${category.name}</h5>
                        </div>
                    </div>
                </div>
            `);
        });
    }

    // Cargar platos de una categoría
    function loadItems(categoryId) {
        const category = categories.find(c => c.id == categoryId);
        $('#categoryTitle').text(category.name);
        
        const $container = $('#itemsContainer');
        $container.empty();
        
        if (items[categoryId]) {
            items[categoryId].forEach(item => {
                $container.append(`
                    <div class="col-md-4">
                        <div class="item-card" data-id="${item.id}" data-price="${item.price}">
                            <div class="position-relative">
                                <img src="${item.img}" class="item-img w-100">
                                <span class="item-price">$${item.price.toFixed(2)}</span>
                            </div>
                            <div class="card-footer text-center bg-success text-white">
                                <h5>${item.name}</h5>
                            </div>
                        </div>
                    </div>
                `);
            });
        }
        
        $('#categoriesPanel').hide();
        $('#itemsPanel').show();
    }

    // Actualizar ticket
    function updateTicket() {
        const $container = $('#ticketItems');
        
        if (ticketItems.length === 0) {
            $container.html('<p class="text-center text-muted">No hay productos agregados</p>');
        } else {
            $container.empty();
            ticketItems.forEach(item => {
                $container.append(`
                    <div class="ticket-item">
                        <div class="d-flex justify-content-between">
                            <span>${item.name} x${item.quantity}</span>
                            <span>$${(item.price * item.quantity).toFixed(2)}</span>
                        </div>
                    </div>
                `);
            });
        }
        
        calculateTotals();
    }

    // Calcular totales
    function calculateTotals() {
        subtotal = ticketItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const tax = subtotal * taxRate;
        const total = subtotal + tax - discount;
        
        $('#subtotal').text(`$${subtotal.toFixed(2)}`);
        $('#tax').text(`$${tax.toFixed(2)}`);
        $('#discount').text(`$${discount.toFixed(2)}`);
        $('#total').text(`$${total.toFixed(2)}`);
    }

    // Event Listeners
    $(document).on('click', '.category-card', function() {
        const categoryId = $(this).data('id');
        loadItems(categoryId);
    });

    $(document).on('click', '.item-card', function() {
        const itemId = $(this).data('id');
        const itemPrice = $(this).data('price');
        const itemName = $(this).find('.card-footer h5').text();
        
        // Verificar si ya existe en el ticket
        const existingItem = ticketItems.find(item => item.id === itemId);
        
        if (existingItem) {
            existingItem.quantity++;
        } else {
            ticketItems.push({
                id: itemId,
                name: itemName,
                price: itemPrice,
                quantity: 1
            });
        }
        
        updateTicket();
    });

    $('#backToCategories').click(function() {
        $('#itemsPanel').hide();
        $('#categoriesPanel').show();
    });

    $('#clearBtn').click(function() {
        ticketItems = [];
        discount = 0;
        updateTicket();
    });

    $('#checkoutBtn').click(function() {
        if (ticketItems.length === 0) {
            alert('No hay productos en el ticket');
            return;
        }
        
        // Aquí iría la lógica de cobro (impresión, pago, etc.)
        alert('Venta realizada con éxito!');
        ticketItems = [];
        discount = 0;
        updateTicket();
    });

    // Inicializar
    loadCategories();
});