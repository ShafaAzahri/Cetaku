<style>
    /* Halaman keseluruhan */
    .content-wrapper {
        background-color: #f8f9fa;
    }
    
    /* Card style */
    .detail-card {
        background: white;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .detail-card h5 {
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    /* Progress Track / Status Bar */
    .progress-track {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 20px 0;
    }
    
    .progress-track li {
        flex: 1;
        position: relative;
        text-align: center;
    }
    
    .progress-track .step {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #dee2e6;
        color: white;
        line-height: 40px;
        margin: 0 auto 8px;
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .progress-track .step-label {
        font-size: 12px;
        color: #6c757d;
    }
    
    .progress-track li.active .step {
        background-color: #007bff;
    }
    
    .progress-track li.completed .step {
        background-color: #28a745;
    }
    
    .progress-track li.current .step {
        background-color: #007bff;
    }
    
    /* Information row style */
    .info-row {
        display: flex;
        margin-bottom: 15px;
    }
    
    .info-label {
        width: 40%;
        color: #6c757d;
    }
    
    .info-value {
        width: 60%;
        font-weight: 500;
    }
    
    /* Produk collapse styling */
    .produk-item {
        padding: 10px 15px;
        border-radius: 5px;
        border: 1px solid #eee;
        margin-bottom: 10px;
        cursor: pointer;
    }
    
    .produk-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .produk-header .produk-title {
        display: flex;
        align-items: center;
    }
    
    .produk-header .produk-title i {
        margin-right: 10px;
    }
    
    .produk-header .chevron {
        transition: transform 0.3s;
    }
    
    .produk-header[aria-expanded="true"] .chevron {
        transform: rotate(180deg);
    }
    
    .produk-content {
        padding-top: 15px;
        margin-top: 15px;
        border-top: 1px solid #eee;
    }
    
    /* Total section */
    .total-section {
        text-align: right;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    
    .total-section .subtotal, 
    .total-section .ongkir {
        color: #6c757d;
        margin-bottom: 5px;
    }
    
    .total-section .total {
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    /* Action buttons */
    .action-btn {
        display: block;
        width: 100%;
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 5px;
        text-align: center;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .action-btn.btn-complete {
        background-color: #00BCD4;
        color: white;
        border: none;
    }
    
    .action-btn.btn-ship {
        background-color: #4361ee;
        color: white;
        border: none;
    }
    
    .action-btn.btn-upload {
        background-color: white;
        color: #4361ee;
        border: 1px solid #4361ee;
    }
    
    .action-btn.btn-cancel {
        background-color: #ef4444;
        color: white;
        border: none;
    }
    
    .action-btn i {
        margin-right: 5px;
    }
    
    /* Update Status Form */
    .status-form select,
    .status-form textarea {
        margin-bottom: 15px;
    }
    
    .status-form button {
        width: 100%;
        padding: 12px;
    }
    
    /* Design preview styling */
    .design-preview {
        position: relative;
        margin-bottom: 15px;
    }

    .design-preview img {
        cursor: pointer;
        transition: all 0.2s;
        max-width: 100%;
        border: 1px solid #dee2e6;
    }

    .design-preview img:hover {
        transform: scale(1.03);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .cursor-pointer {
        cursor: pointer;
    }

    /* Styling untuk tab */
    .nav-tabs .nav-link {
        padding: 8px 15px;
        border-radius: 0;
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        background-color: #f8f9fa;
        border-bottom-color: #f8f9fa;
    }
</style>