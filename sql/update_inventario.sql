update inventario_osa_lr4
  set
  date_received = ':date_received',
  osfm_item = ':osfm_item',
  comments = ':comments',
  status = ":status",
where
  serial_num = ':serial_num'