update inventario_osa_lr4
  set
  date_received = to_date(':date_received','yyyy-mm-dd hh24:mi'),
  osfm_item = ':osfm_item',
  comments = ':comments',
  osfm_location = ':osfm_location',
  status = ':status'
where
  serial_num = ':serial_num'