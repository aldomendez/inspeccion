insert INTO inventario_osa_lr4
(DATE_RECEIVED,
 carrier_site,
 serial_num,
 carrier_serial_num,
 status,
 db_status,
 osfm_item,
 comments) 
VALUES
(sysdate,
  ':carrier_site',
  ':serial_num',
  ':carrier_serial_num',
  ':status',
  ':db_status',
  '',
  ''
)
