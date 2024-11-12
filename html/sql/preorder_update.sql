-- Changes which transaction is a preorder.
UPDATE preorders
   SET transaction_id = ?;
 WHERE transaction_id = ?;
