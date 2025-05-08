const blacklistedCount = document.getElementById('blacklisted-supplier-count');

if (activeCount) {
    activeCount.textContent = suppliers.filter(s => s.status === 'Active').length;
}
if (inactiveCount) {
    inactiveCount.textContent = suppliers.filter(s => s.status === 'Inactive').length;
}
if (pendingCount) {
    pendingCount.textContent = suppliers.filter(s => s.status === 'Pending').length;
}
if (blacklistedCount) {
    blacklistedCount.textContent = suppliers.filter(s => s.status === 'Blacklisted').length;
}


/**
* Search suppliers by name or company name
* @param {string} keyword
*/
function searchSuppliers(keyword) {
    const lowerKeyword = keyword.toLowerCase();
    filteredSuppliers = suppliers.filter(supplier =>
        supplier.name.toLowerCase().includes(lowerKeyword) ||
        supplier.company_name.toLowerCase().includes(lowerKeyword)
    );
    currentPage = 1;
    displaySuppliers(filteredSuppliers);
}

/**
* Filter suppliers by status
* @param {string} status
*/
function filterSuppliersByStatus(status) {
    if (status === 'All') {
        filteredSuppliers = [...suppliers];
    } else {
        filteredSuppliers = suppliers.filter(supplier => supplier.status === status);
    }
    currentPage = 1;
    displaySuppliers(filteredSuppliers);
}

/**
* Show an error message to the user
* @param {string} message
*/
function showErrorMessage(message) {
    const alertContainer = document.getElementById('alert-container');
    if (alertContainer) {
        alertContainer.innerHTML = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    } else {
        alert(message); // fallback
    }
}

/**
* Placeholder function to view a supplier
* @param {number} id
*/
function viewSupplier(id) {
    console.log('View supplier:', id);
    // Implement modal or redirect logic here
}

/**
* Placeholder function to edit a supplier
* @param {number} id
*/
function editSupplier(id) {
    console.log('Edit supplier:', id);
    // Implement modal pre-fill or fetch logic here
}

/**
* Confirm and delete a supplier
* @param {number} id
*/
function confirmDeleteSupplier(id) {
    if (confirm('Are you sure you want to delete this supplier?')) {
        deleteSupplier(id);
    }
}

/**
* Delete a supplier by ID
* @param {number} id
*/
function deleteSupplier(id) {
    fetch(`../components/supplier_operations.php?action=delete_supplier&id=${id}`, {
        method: 'DELETE'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                suppliers = suppliers.filter(s => s.id !== id);
                filteredSuppliers = filteredSuppliers.filter(s => s.id !== id);
                displaySuppliers(filteredSuppliers);
                updateSupplierCountBadge();
                showSuccessMessage('Supplier deleted successfully.');
            } else {
                showErrorMessage('Failed to delete supplier: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error deleting supplier:', error);
            showErrorMessage('Failed to delete supplier. Please try again later.');
        });
}

/**
* Show a success message
* @param {string} message
*/
function showSuccessMessage(message) {
    const alertContainer = document.getElementById('alert-container');
    if (alertContainer) {
        alertContainer.innerHTML = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    } else {
        alert(message); // fallback
    }
}
