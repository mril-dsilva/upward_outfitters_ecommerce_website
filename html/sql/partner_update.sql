UPDATE partners
   SET partner_name = (?),
       address_id = (?),
       partner_phone_number = (?),
       partner_email = (?)
 WHERE partner_id = (?);