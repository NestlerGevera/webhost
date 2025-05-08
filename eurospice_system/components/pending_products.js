document.addEventListener('DOMContentLoaded', () => {
    fetch('../components/get_pending_orders.php')
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#pendingOrdersTable tbody');
            tbody.innerHTML = ''; // Clear existing rows

            let count = 0;

            data.forEach(order => {
                count++;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${order.order_id}</td>
                    <td>${order.customer_name}</td>
                    <td>${order.brand}</td>
                    <td>${order.stock} units</td>
                    <td>₱${parseFloat(order.price).toLocaleString()}</td>
                    <td>${order.batch_code}</td>
                    <td>${order.weight}</td>
                    <td>${order.pack_type}</td>
                    <td>${order.pack_size}</td>
                    <td>${order.shelf_type}</td>
                    <td>${new Date(order.expiration_date).toLocaleDateString()}</td>
                    <td>${order.country}</td>
                    <td>${order.delivered_status}</td>
                    <td><img src="${order.image_path}" alt="Order image" width="50"></td>
                    <td>
                        <button onclick="viewOrderDetails('${order.order_id}')">View</button>
                        <button onclick="approveOrder('${order.order_id}')">Approve</button>
                        <button onclick="rejectOrder('${order.order_id}')">Reject</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            // Optionally update the live counter if used elsewhere
            const counter = document.getElementById('pendingOrdersCount');
            if (counter) counter.textContent = count;
        })
        .catch(err => {
            console.error('Error loading pending orders:', err);
        });
});

function approveOrder(orderId) {
    fetch('components/approve_product.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `order_id=${orderId}`
    }).then(res => res.text()).then(alert);
}

function rejectOrder(orderId) {
    fetch('components/reject_product.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `order_id=${orderId}`
    }).then(res => res.text()).then(alert);
}
