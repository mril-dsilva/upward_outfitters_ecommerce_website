UPDATE transactions
   SET transaction_timestamp = ?,
       partner_id = ?,
       shipment_id = ?,
       employee_id = ?,
       location_id = ?
 WHERE transaction_id = ?;
