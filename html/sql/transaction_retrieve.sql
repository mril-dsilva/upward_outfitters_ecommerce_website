-- Get all transactions.
SELECT transactions.transaction_id,
       transaction_timestamp,
       partner_name,
       shipment_id,
       employee_name,
       transactions.location_id,
       GROUP_CONCAT(CONCAT(product_name, " x ", transaction_product_quantity) SEPARATOR ", ") AS products,
       CONCAT("$", SUM(transaction_product_price * transaction_product_quantity)) AS amount_transacted
  FROM transactions
       LEFT JOIN partners ON partners.partner_id = transactions.partner_id
       LEFT JOIN employees ON employees.employee_id = transactions.employee_id
       LEFT JOIN transaction_products ON transaction_products.transaction_id = transactions.transaction_id
       LEFT JOIN products ON products.product_id = transaction_products.product_id
 GROUP BY transaction_id
