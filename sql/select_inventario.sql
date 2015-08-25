select
  DATE_RECEIVED,
  carrier_site,
  serial_num,
  carrier_serial_num,
  status actual_status,
  db_status status,
  osfm_item item,
  comments
from inventario_osa_lr4
where carrier_serial_num = ':carrier_serial_num'