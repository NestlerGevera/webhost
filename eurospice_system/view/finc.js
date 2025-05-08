

// Display current date and time
function updateDateTime() {
    const now = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
}
updateDateTime();
setInterval(updateDateTime, 60000); // Update every minute

// Tab functionality
function showTab(tabId) {
    // Hide all tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => {
        tab.classList.remove('active');
    });

    // Deactivate all tab buttons
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(button => {
        button.classList.remove('active');
    });

    // Show the selected tab
    const selectedTab = document.getElementById(tabId);
    if (selectedTab) {
        selectedTab.classList.add('active');
    }

    // Activate the corresponding button (if exists)
    const activeButton = document.querySelector(`.tab-btn[onclick="showTab('${tabId}')"]`);
    if (activeButton) {
        activeButton.classList.add('active');
    }
}

// Sidebar dropdown menus
function toggleMenu(menuId) {
    const menu = document.getElementById(menuId);
    if (menu) {
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }
}

// Modal functionality
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

let pendingCount = 0; // Starting value

function updatePendingOrdersCount() {
    const countElement = document.getElementById("pendingOrdersCount");
    if (countElement) {
        countElement.textContent = pendingCount;
    }
}

function openProductDetails(product) {
    document.getElementById('modalProductId').textContent = product.id;
    document.getElementById('approvalProductId').value = product.id;
    // Set other fields...
    openModal('productDetailsModal');
}


function finalizeApproval() {
    const orderId = document.getElementById('approvalOrderId').value;
    const notes = document.getElementById('approvalNotes').value;

    if (!orderId) {
        alert("Order ID is missing.");
        return;
    }

    if (confirm(`Are you sure you want to approve order ${orderId}?`)) {
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('notes', notes);

        fetch('approve_order.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.text())
            .then(response => {
                alert(`✅ Order ${orderId} approved successfully.`);
                closeModal('orderDetailsModal');
                // Optionally reload or move order to Approved tab
            })
            .catch(err => {
                console.error(err);
                alert("❌ Failed to approve order.");
            });
    }
}

function finalizeRejection(orderId) {
    const notes = document.getElementById('approvalNotes').value;

    if (!notes.trim()) {
        alert("⚠️ Please provide a reason for rejection.");
        return;
    }

    if (confirm(`Are you sure you want to reject order ${orderId}?`)) {
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('notes', notes);

        fetch('reject_order.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.text())
            .then(response => {
                alert(`🚫 Order ${orderId} rejected successfully.`);
                closeModal('orderDetailsModal');
                // Optionally reload or move order to Rejected tab
            })
            .catch(err => {
                console.error(err);
                alert("❌ Failed to reject order.");
            });
    }
}



// Budget management
function adjustBudget(department) {
    document.getElementById('budgetDepartment').textContent = department;

    // Set current budget (in a real app, you would fetch this)
    let currentBudget = "₱0.00";
    switch (department) {
        case 'Warehouse':
            currentBudget = "₱1,500,000.00";
            break;
        case 'Logistics':
            currentBudget = "₱1,200,000.00";
            break;
        case 'Sales':
            currentBudget = "₱800,000.00";
            break;
        case 'Admin':
            currentBudget = "₱600,000.00";
            break;
    }

    document.getElementById('currentBudget').value = currentBudget;
    openModal('adjustBudgetModal');
}

function saveBudgetAdjustment() {
    const department = document.getElementById('budgetDepartment').textContent;
    const newBudget = document.getElementById('budgetAdjustment').value;
    const reason = document.getElementById('adjustmentReason').value;

    if (!newBudget) {
        alert("Please enter a new budget amount.");
        return;
    }

    if (!reason.trim()) {
        alert("Please provide a reason for the budget adjustment.");
        return;
    }

    // In a real application, you would send this information to the server
    alert(`Budget for ${department} updated to ₱${parseFloat(newBudget).toLocaleString()} with reason: ${reason}`);
    closeModal('adjustBudgetModal');
    // You might want to refresh the budget table
}

function viewBudgetHistory(department) {
    document.getElementById('historyDepartment').textContent = department;

    // In a real application, you would fetch budget history from the server
    // For now, we'll populate with sample data
    const historyTable = document.getElementById('budgetHistoryTable').getElementsByTagName('tbody')[0];
    historyTable.innerHTML = ''; // Clear existing rows

    // Sample data
    const historyData = [
        {
            date: 'May 01, 2025',
            previousBudget: '₱1,200,000.00',
            newBudget: '₱1,500,000.00',
            change: '+₱300,000.00',
            reason: 'Quarterly budget increase',
            adjustedBy: 'Maria Santos'
        },
        {
            date: 'Feb 15, 2025',
            previousBudget: '₱1,000,000.00',
            newBudget: '₱1,200,000.00',
            change: '+₱200,000.00',
            reason: 'Operational expansion',
            adjustedBy: 'Maria Santos'
        },
        {
            date: 'Jan 01, 2025',
            previousBudget: '₱1,100,000.00',
            newBudget: '₱1,000,000.00',
            change: '-₱100,000.00',
            reason: 'Annual budget reallocation',
            adjustedBy: 'John Reyes'
        }
    ];

    // Add rows to the table
    historyData.forEach(item => {
        const row = historyTable.insertRow();
        row.insertCell(0).textContent = item.date;
        row.insertCell(1).textContent = item.previousBudget;
        row.insertCell(2).textContent = item.newBudget;
        row.insertCell(3).textContent = item.change;
        row.insertCell(4).textContent = item.reason;
        row.insertCell(5).textContent = item.adjustedBy;
    });

    openModal('budgetHistoryModal');
}

// Reports generation
function generateBudgetReport() {
    const period = document.getElementById('reportPeriod').value;
    const reportContainer = document.getElementById('budgetReportContainer');

    // In a real application, you would fetch this data from the server
    // For now, we'll just display some sample HTML
    reportContainer.innerHTML = `
        <h3>${period.charAt(0).toUpperCase() + period.slice(1)} Budget Report</h3>
        <p>Generated on: ${new Date().toLocaleDateString()}</p>
        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Allocated Budget</th>
                    <th>Used Budget</th>
                    <th>Remaining Budget</th>
                    <th>Usage Percentage</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Warehouse</td>
                    <td>₱1,500,000.00</td>
                    <td>₱875,450.00</td>
                    <td>₱624,550.00</td>
                    <td>58.36%</td>
                </tr>
                <tr>
                    <td>Logistics</td>
                    <td>₱1,200,000.00</td>
                    <td>₱534,780.00</td>
                    <td>₱665,220.00</td>
                    <td>44.57%</td>
                </tr>
                <tr>
                    <td>Sales</td>
                    <td>₱800,000.00</td>
                    <td>₱344,890.00</td>
                    <td>₱455,110.00</td>
                    <td>43.11%</td>
                </tr>
                <tr>
                    <td>Admin</td>
                    <td>₱600,000.00</td>
                    <td>₱245,680.00</td>
                    <td>₱354,320.00</td>
                    <td>40.95%</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td>Total</td>
                    <td>₱4,100,000.00</td>
                    <td>₱2,000,800.00</td>
                    <td>₱2,099,200.00</td>
                    <td>48.80%</td>
                </tr>
            </tbody>
        </table>
    `;
}

function generateDailyReport() {
    const date = document.getElementById('dailyReportDate').value;
    const reportContainer = document.getElementById('dailyReportContainer');

    // Format the date nicely
    const formattedDate = new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    // In a real application, you would fetch this data from the server
    reportContainer.innerHTML = `
        <h3>Daily Financial Report: ${formattedDate}</h3>
        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
            <div style="background: #f2f2f2; padding: 20px; border-radius: 8px; flex: 1;">
                <h4>Daily Income</h4>
                <p style="font-size: 24px; font-weight: bold;">₱325,780.00</p>
            </div>
            <div style="background: #f2f2f2; padding: 20px; border-radius: 8px; flex: 1;">
                <h4>Daily Expenses</h4>
                <p style="font-size: 24px; font-weight: bold;">₱245,320.00</p>
            </div>
            <div style="background: #f2f2f2; padding: 20px; border-radius: 8px; flex: 1;">
                <h4>Net Profit</h4>
                <p style="font-size: 24px; font-weight: bold;">₱80,460.00</p>
            </div>
        </div>
        <h4>Transaction Summary</h4>
        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <th>Transaction Type</th>
                    <th>Count</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sales</td>
                    <td>42</td>
                    <td>₱325,780.00</td>
                </tr>
                <tr>
                    <td>Purchases</td>
                    <td>15</td>
                    <td>₱178,560.00</td>
                </tr>
                <tr>
                    <td>Expenses</td>
                    <td>23</td>
                    <td>₱66,760.00</td>
                </tr>
            </tbody>
        </table>
    `;
}

function generateMonthlyReport() {
    const monthIndex = document.getElementById('monthSelect').value;
    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    const reportContainer = document.getElementById('monthlyReportContainer');

    // In a real application, you would fetch this data from the server
    reportContainer.innerHTML = `
        <h3>Monthly Financial Report: ${monthNames[monthIndex - 1]} 2025</h3>
        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
            <div style="background: #f2f2f2; padding: 20px; border-radius: 8px; flex: 1;">
                <h4>Monthly Income</h4>
                <p style="font-size: 24px; font-weight: bold;">₱5,867,920.00</p>
            </div>
            <div style="background: #f2f2f2; padding: 20px; border-radius: 8px; flex: 1;">
                <h4>Monthly Expenses</h4>
                <p style="font-size: 24px; font-weight: bold;">₱3,452,680.00</p>
            </div>
            <div style="background: #f2f2f2; padding: 20px; border-radius: 8px; flex: 1;">
                <h4>Net Profit</h4>
                <p style="font-size: 24px; font-weight: bold;">₱2,415,240.00</p>
            </div>
        </div>
        <h4>Department Breakdown</h4>
        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Income</th>
                    <th>Expenses</th>
                    <th>Net</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sales</td>
                    <td>₱5,867,920.00</td>
                    <td>₱1,245,780.00</td>
                    <td>₱4,622,140.00</td>
                </tr>
                <tr>
                    <td>Warehouse</td>
                    <td>₱0.00</td>
                    <td>₱865,450.00</td>
                    <td>-₱865,450.00</td>
                </tr>
                <tr>
                    <td>Logistics</td>
                    <td>₱0.00</td>
                    <td>₱954,730.00</td>
                    <td>-₱954,730.00</td>
                </tr>
                <tr>
                    <td>Admin</td>
                    <td>₱0.00</td>
                    <td>₱386,720.00</td>
                    <td>-₱386,720.00</td>
                </tr>
            </tbody>
        </table>
    `;
}

function generateYearlyReport() {
    const year = document.getElementById('yearSelect').value;
    const reportContainer = document.getElementById('yearlyReportContainer');

    // In a real application, you would fetch this data from the server
    reportContainer.innerHTML = `
        <h3>Yearly Financial Report: ${year}</h3>
        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
            <div style="background: #f2f2f2; padding: 20px; border-radius: 8px; flex: 1;">
                <h4>Annual Income</h4>
                <p style="font-size: 24px; font-weight: bold;">₱38,765,920.00</p>
            </div>
            <div style="background: #f2f2f2; padding: 20px; border-radius: 8px; flex: 1;">
                <h4>Annual Expenses</h4>
                <p style="font-size: 24px; font-weight: bold;">₱25,678,350.00</p>
            </div>
            <div style="background: #f2f2f2; padding: 20px; border-radius: 8px; flex: 1;">
                <h4>Net Profit</h4>
                <p style="font-size: 24px; font-weight: bold;">₱13,087,570.00</p>
            </div>
        </div>
        <h4>Quarterly Performance</h4>
        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <th>Quarter</th>
                    <th>Income</th>
                    <th>Expenses</th>
                    <th>Net Profit</th>
                    <th>Profit Margin</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Q1 (Jan-Mar)</td>
                    <td>₱12,345,670.00</td>
                    <td>₱8,765,430.00</td>
                    <td>₱3,580,240.00</td>
                    <td>29.00%</td>
                </tr>
                <tr>
                    <td>Q2 (Apr-Jun)</td>
                    <td>₱10,876,540.00</td>
                    <td>₱6,987,650.00</td>
                    <td>₱3,888,890.00</td>
                    <td>35.75%</td>
                </tr>
                <tr>
                    <td>Q3 (Jul-Sep)</td>
                    <td>₱8,765,430.00</td>
                    <td>₱5,432,100.00</td>
                    <td>₱3,333,330.00</td>
                    <td>38.03%</td>
                </tr>
                <tr>
                    <td>Q4 (Oct-Dec)</td>
                    <td>₱6,778,280.00</td>
                    <td>₱4,493,170.00</td>
                    <td>₱2,285,110.00</td>
                    <td>33.71%</td>
                </tr>
            </tbody>
        </table>
    `;
}

// Search functionality
function searchOrders() {
    const searchTerm = document.getElementById('searchOrders').value.toLowerCase();
    const pendingTable = document.getElementById('pendingOrdersTable');
    const approvedTable = document.getElementById('approvedOrdersTable');
    const rejectedTable = document.getElementById('rejectedOrdersTable');

    // Function to search within a table
    function searchTable(table) {
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].textContent.toLowerCase();

                if (cellText.includes(searchTerm)) {
                    found = true;
                    break;
                }
            }

            rows[i].style.display = found ? '' : 'none';
        }
    }

    // Search in all tables
    searchTable(pendingTable);
    searchTable(approvedTable);
    searchTable(rejectedTable);
}

function resetSearch() {
    document.getElementById('searchOrders').value = '';

    // Show all rows in all tables
    const tables = [
        document.getElementById('pendingOrdersTable'),
        document.getElementById('approvedOrdersTable'),
        document.getElementById('rejectedOrdersTable')
    ];

    tables.forEach(table => {
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        for (let i = 0; i < rows.length; i++) {
            rows[i].style.display = '';
        }
    });
}


// Handle incoming messages from the server
function handleServerMessage(data) {
    // Process different types of server messages
    switch (data.type) {
        case 'orderUpdate':
            // Handle order updates (e.g., new orders, status changes)
            updateOrderStatus(data.orderId, data.status);
            break;
        case 'budgetUpdate':
            // Handle budget updates
            updateBudgetDisplay(data.department, data.amount);
            break;
        case 'notification':
            // Handle notifications
            showNotification(data.message);
            break;
        default:
            console.log('Unknown message type:', data.type);
    }
}

// Update order status in the UI
function updateOrderStatus(orderId, status) {
    // Find the order in the tables and update its status
    const tables = [
        document.getElementById('pendingOrdersTable'),
        document.getElementById('approvedOrdersTable'),
        document.getElementById('rejectedOrdersTable')
    ];

    tables.forEach(table => {
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        for (let i = 0; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            if (cells[0].textContent === orderId) {
                // Move the row to the appropriate table based on status
                // This is a simplified example - in a real app, you might refresh the data
                alert(`Order ${orderId} status changed to ${status}`);
                return;
            }
        }
    });
}

// Update budget display in the UI
function updateBudgetDisplay(department, amount) {
    // Find the department in the budget table and update its amount
    const budgetTable = document.getElementById('budgetAllocationTable');
    const rows = budgetTable.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        if (cells[1].textContent === department) {
            // Update the budget amount
            cells[2].textContent = `₱${parseFloat(amount).toLocaleString()}.00`;
            // You might need to update other cells too
            return;
        }
    }
}

// Show a notification to the user
function showNotification(message) {
    // In a real app, you might use a toast notification library
    alert(message);
}


function openProductModal(product) {
    // Populate modal with product details
    document.getElementById('modalProductId').innerText = product.id;
    document.getElementById('modalProductName').innerText = product.name;
    document.getElementById('modalProductBrand').innerText = product.brand;
    document.getElementById('modalProductStock').innerText = product.stock;
    document.getElementById('modalProductPrice').innerText = '₱' + parseFloat(product.price).toFixed(2);
    document.getElementById('modalProductBatchCode').innerText = product.batch_code;
    document.getElementById('modalProductWeight').innerText = product.weight;
    document.getElementById('modalProductPackType').innerText = product.pack_type;
    document.getElementById('modalProductPackSize').innerText = product.pack_size;
    document.getElementById('modalProductShelfType').innerText = product.shelf_type;
    document.getElementById('modalProductExpiration').innerText = product.expiration;
    document.getElementById('modalProductCountry').innerText = product.country;
    document.getElementById('modalProductDelivered').innerText = product.delivered;

    // Set image if available
    if (product.image) {
        document.getElementById('modalProductImage').innerHTML = `<img src="../uploads/${product.image}" alt="Product Image" style="max-width: 200px;">`;
    } else {
        document.getElementById('modalProductImage').innerHTML = 'No image available';
    }

    document.getElementById('modalProductNotes').innerText = product.notes;

    // Show the modal
    const productModal = new bootstrap.Modal(document.getElementById('productDetailsModal'));
    productModal.show();
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function approveProduct(productId) {
    if (!confirm('Are you sure you want to approve this product?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'approve_product');
    formData.append('product_id', productId);

    fetch('../components/finance_product_operations.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Product approved successfully');
                // Reload the list
                loadPendingProducts();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error approving product:', error);
            alert('Failed to approve product. Please try again later.');
        });
}

function rejectProduct(productId) {
    const notes = prompt('Please provide a reason for rejection:');

    if (notes === null) {
        // User cancelled
        return;
    }

    if (notes.trim() === '') {
        alert('Rejection reason is required');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'reject_product');
    formData.append('product_id', productId);
    formData.append('notes', notes);

    fetch('../components/finance_product_operations.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Product rejected successfully');
                // Reload the list
                loadPendingProducts();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error rejecting product:', error);
            alert('Failed to reject product. Please try again later.');
        });
}

// Add these functions to finc.js

// Global variable to store pending products count
let pendingProductsCount = 0;

// Function to load pending products
function loadPendingProducts() {
    fetch('../components/finance_product_operations.php?action=get_pending_products', {
        method: 'GET'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // Process the returned data
            if (data.success) {
                displayPendingProducts(data.products);
            } else {
                console.error('Error from server:', data.message);
                // Check if element exists before using it
                const container = document.getElementById('pending-products-container');
                if (container) {
                    container.innerHTML = `<div class="alert alert-danger">Failed to load pending products: ${data.message}</div>`;
                }
            }
        })
        .catch(error => {
            console.error('Error loading pending products:', error);
            // Check if element exists before using it
            const container = document.getElementById('pending-products-container');
            if (container) {
                container.innerHTML = '<div class="alert alert-danger">Failed to load pending products. Please try again later.</div>';
            }
        });
}



function displayPendingProducts(products, containerId = 'pending-products-container') {
    // Allow specifying a different container ID or use default
    const container = document.getElementById(containerId);

    // Check if container exists
    if (!container) {
        console.error(`Error: Element with ID "${containerId}" not found`);

        // Create the container if it doesn't exist
        const newContainer = document.createElement('div');
        newContainer.id = containerId;

        // Try to append to common parent elements
        const possibleParents = [
            document.querySelector('.product-dashboard'),
            document.querySelector('.admin-panel'),
            document.querySelector('.content'),
            document.querySelector('main'),
            document.body
        ];

        // Find first available parent
        const parent = possibleParents.find(el => el !== null);

        if (parent) {
            parent.appendChild(newContainer);
            console.log(`Created missing container with ID "${containerId}"`);
            return displayPendingProducts(products, containerId); // Retry with newly created container
        } else {
            console.error('Could not find a suitable parent element to create container');
            return;
        }
    }

    // Clear existing content
    container.innerHTML = '';

    // Check if products array is empty or invalid
    if (!products || !Array.isArray(products) || products.length === 0) {
        container.innerHTML = '<div class="empty-state"><p>No pending products available</p></div>';
        return;
    }

    // Create header row for products
    const headerRow = document.createElement('div');
    headerRow.className = 'product-header-row';
    headerRow.innerHTML = `
        <span class="header-item header-name">Product Name</span>
        <span class="header-item header-price">Price</span>
        <span class="header-item header-status">Status</span>
        <span class="header-item header-actions">Actions</span>
    `;
    container.appendChild(headerRow);

    // Create product list container
    const productList = document.createElement('div');
    productList.className = 'product-list';
    container.appendChild(productList);

    // Create and append product elements
    products.forEach(product => {
        if (!product || typeof product !== 'object') return;

        const productId = product.id || 'unknown';
        const productName = product.name || 'Unnamed Product';
        const productDesc = product.description || 'No description available';
        const productPrice = typeof product.price === 'number' ?
            `$${product.price.toFixed(2)}` : 'Price not set';
        const productStatus = product.status || 'Unknown Status';

        const productElement = document.createElement('div');
        productElement.className = 'product-item';
        productElement.dataset.id = productId;

        productElement.innerHTML = `
            <div class="product-main">
                <div class="product-info">
                    <h3 class="product-name">${productName}</h3>
                    <p class="product-description">${productDesc}</p>
                </div>
                <div class="product-price">${productPrice}</div>
                <div class="product-status status-${productStatus.toLowerCase().replace(/\s+/g, '-')}">
                    ${productStatus}
                </div>
                <div class="product-actions">
                    <button class="action-btn approve-btn" 
                            onclick="handleProductAction(${productId}, 'approve')" 
                            aria-label="Approve Product">
                        <span class="btn-icon">✓</span> Approve
                    </button>
                    <button class="action-btn reject-btn" 
                            onclick="handleProductAction(${productId}, 'reject')" 
                            aria-label="Reject Product">
                        <span class="btn-icon">✕</span> Reject
                    </button>
                </div>
            </div>
        `;

        productList.appendChild(productElement);
    });

    // Add event listeners for expand/collapse if needed
    const productItems = container.querySelectorAll('.product-item');
    productItems.forEach(item => {
        item.addEventListener('click', function (e) {
            // Ignore clicks on buttons
            if (e.target.tagName === 'BUTTON' || e.target.closest('button')) return;

            // Toggle expanded class for detailed view
            this.classList.toggle('expanded');
        });
    });
}


// Update pending products count badge
function updatePendingProductsCount(count) {
    pendingProductsCount = count;
    const countBadge = document.getElementById('pendingProductsCount');
    if (countBadge) {
        countBadge.textContent = count;
    }
}

// Open product details modal
function openProductDetails(product) {
    // Set product details in modal
    document.getElementById('modalProductId').textContent = product.id;
    document.getElementById('modalProductName').textContent = product.name;
    document.getElementById('modalProductBrand').textContent = product.brand || '';
    document.getElementById('modalProductStock').textContent = product.stock || '0';
    document.getElementById('modalProductPrice').textContent = '₱' + parseFloat(product.price || 0).toFixed(2);
    document.getElementById('modalProductBatchCode').textContent = product.batchCode || '';
    document.getElementById('modalProductWeight').textContent = product.weight || '';
    document.getElementById('modalProductPackType').textContent = product.packtype || '';
    document.getElementById('modalProductPackSize').textContent = product.packsize || '';
    document.getElementById('modalProductShelfType').textContent = product.shelftype || '';
    document.getElementById('modalProductExpiration').textContent = formatDate(product.expirationDate) || '';
    document.getElementById('modalProductCountry').textContent = product.country || '';
    document.getElementById('modalProductDelivered').textContent = formatDate(product.delivered) || '';
    document.getElementById('modalProductNotes').textContent = product.supplier_notes || 'No notes provided';

    // Set product ID for approval form
    document.getElementById('approvalProductId').value = product.id;

    // Clear previous notes
    document.getElementById('approvalNotes').value = '';

    // Set image if available
    if (product.image) {
        document.getElementById('modalProductImage').innerHTML = `
            <img src="../uploads/${product.image}" alt="Product Image" class="img-fluid">
        `;
    } else {
        document.getElementById('modalProductImage').innerHTML = `
            <div class="p-5 bg-light text-center">
                <i class="fas fa-image fa-3x text-muted"></i>
                <p class="mt-2">No image available</p>
            </div>
        `;
    }

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('productDetailsModal'));
    modal.show();
}

// Format date helper function
function formatDate(dateString) {
    if (!dateString) return '';

    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';

    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Approve product
function approveProduct() {
    const productId = document.getElementById('approvalProductId').value;
    const notes = document.getElementById('approvalNotes').value;

    if (!productId) {
        alert('Product ID is missing');
        return;
    }

    if (confirm(`Are you sure you want to approve this product?`)) {
        const formData = new FormData();
        formData.append('action', 'approve_product');
        formData.append('product_id', productId);
        formData.append('notes', notes);

        fetch('../components/finance_product_operations.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Product approved successfully');

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('productDetailsModal'));
                    modal.hide();

                    // Reload pending products
                    loadPendingProducts();
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Failed to approve product');
            });
    }
}

// Reject product
function rejectProduct() {
    const productId = document.getElementById('approvalProductId').value;
    const notes = document.getElementById('approvalNotes').value;

    if (!productId) {
        alert('Product ID is missing');
        return;
    }

    if (!notes.trim()) {
        alert('⚠️ Please provide a reason for rejection');
        return;
    }

    if (confirm(`Are you sure you want to reject this product?`)) {
        const formData = new FormData();
        formData.append('action', 'reject_product');
        formData.append('product_id', productId);
        formData.append('notes', notes);

        fetch('../components/finance_product_operations.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('🚫 Product rejected successfully');

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('productDetailsModal'));
                    modal.hide();

                    // Reload pending products
                    loadPendingProducts();
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Failed to reject product');
            });
    }
}

// Search products
function searchProducts() {
    const searchTerm = document.getElementById('searchProducts').value.toLowerCase();
    const table = document.getElementById('pendingProductsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let found = false;

        for (let j = 0; j < cells.length; j++) {
            const cellText = cells[j].textContent.toLowerCase();

            if (cellText.includes(searchTerm)) {
                found = true;
                break;
            }
        }

        rows[i].style.display = found ? '' : 'none';
    }
}

// Reset product search
function resetProductSearch() {
    document.getElementById('searchProducts').value = '';

    const table = document.getElementById('pendingProductsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        rows[i].style.display = '';
    }
}

// Initialize pending products when page loads
document.addEventListener('DOMContentLoaded', function () {
    // Add tab button for pending products
    const tabButtons = document.querySelector('.tab-buttons');
    if (tabButtons) {
        const pendingProductsButton = document.createElement('button');
        pendingProductsButton.className = 'tab-btn';
        pendingProductsButton.setAttribute('onclick', "showTab('pendingProductsTab')");
        pendingProductsButton.innerHTML = 'Product Approvals <span id="pendingProductsBadge" class="badge bg-danger">0</span>';
        tabButtons.appendChild(pendingProductsButton);
    }

    // Load pending products
    loadPendingProducts();

    // Set interval to check for new pending products periodically
    setInterval(loadPendingProducts, 60000); // Check every minute
});

document.querySelector('#pendingProductsTable').addEventListener('click', function (e) {
    if (e.target.classList.contains('view-product-btn')) {
        const productData = e.target.getAttribute('data-product');
        try {
            const product = JSON.parse(productData.replace(/&apos;/g, "'"));
            openProductDetails(product);
        } catch (err) {
            console.error("Failed to parse product data:", err);
        }
    }
});

function handleProductAction(productId, action) {
    if (!productId) {
        console.error('Invalid product ID for action:', action);
        return;
    }

    console.log(`Performing action "${action}" on product ID: ${productId}`);

    // Example implementation
    switch (action) {
        case 'approve':
            // Add your approval logic here
            alert(`Product #${productId} has been approved`);
            // You might want to remove the product from the list or update its status
            break;
        case 'reject':
            // Add your rejection logic here
            const confirmReject = confirm(`Are you sure you want to reject product #${productId}?`);
            if (confirmReject) {
                alert(`Product #${productId} has been rejected`);
                // You might want to remove the product from the list or update its status
            }
            break;
        default:
            console.error('Unknown action:', action);
    }
}