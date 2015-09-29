select
  to_Char(date_received,'yyyy-mm-dd hh24:mi')date_received,
  carrier_site,
  serial_num,
  carrier_serial_num,
  status actual_status,
  db_status,
  status,
  osfm_item item,
  osfm_location,
  comments
from inventario_osa_lr4
where carrier_serial_num = ':carrier_serial_num'