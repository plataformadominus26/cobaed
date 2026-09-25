<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Restaurante</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (Íconos) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .pos-container {
            display: grid;
            grid-template-columns: 70% 30%;
            height: 100vh;
        }
        .categories-panel, .items-panel {
            background: #ffffff;
            padding: 15px;
            overflow-y: auto;
        }
        .ticket-panel {
            background: #343a40;
            color: white;
            padding: 15px;
            overflow-y: auto;
        }
        .category-card, .item-card {
            cursor: pointer;
            transition: transform 0.2s;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }
        .category-card:hover, .item-card:hover {
            transform: scale(1.03);
        }
        .category-img, .item-img {
            height: 120px;
            object-fit: cover;
        }
        .item-price {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .ticket-item {
            border-bottom: 1px solid #495057;
            padding: 8px 0;
        }
        .summary-section {
            background: #495057;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .btn-action {
            font-size: 18px;
            padding: 12px;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="pos-container">
        <!-- Panel de Categorías/Platos -->
        <div class="categories-panel" id="categoriesPanel">
            <h3 class="text-center mb-4">Seleccione una Categoría</h3>
            <div class="row" id="categoriesContainer">
                <!-- Categorías se cargarán aquí -->
            </div>
        </div>
        <div class="items-panel" id="itemsPanel" style="display: none;">
            <button class="btn btn-secondary mb-3" id="backToCategories">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
            <h3 class="text-center mb-4" id="categoryTitle"></h3>
            <div class="row" id="itemsContainer">
                <!-- Platos se cargarán aquí -->
            </div>
        </div>

        <!-- Panel de Ticket y Resumen -->
        <div class="ticket-panel">
            <h3 class="text-center mb-4">Ticket</h3>
            <div id="ticketItems">
                <!-- Items del ticket se cargarán aquí -->
                <p class="text-center text-muted">No hay productos agregados</p>
            </div>
            <div class="summary-section">
                <div class="d-flex justify-content-between">
                    <span>Subtotal:</span>
                    <span id="subtotal">$0.00</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>IVA (16%):</span>
                    <span id="tax">$0.00</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Descuento:</span>
                    <span id="discount">$0.00</span>
                </div>
                <div class="d-flex justify-content-between fw-bold mt-2">
                    <span>Total:</span>
                    <span id="total">$0.00</span>
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-success btn-action w-100" id="checkoutBtn">
                    <i class="fas fa-cash-register"></i> Cobrar
                </button>
                <button class="btn btn-danger btn-action w-100" id="clearBtn">
                    <i class="fas fa-trash"></i> Limpiar
                </button>
            </div>
        </div>
    </div>

    <!-- jQuery & Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script personalizado -->
    <script src="this.js"></script>
</body>
</html>