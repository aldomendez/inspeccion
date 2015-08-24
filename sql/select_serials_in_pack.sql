Select 
  carrier_Serial_num,
  carrier_site,
  serial_num,
  Status
from
  carrier_site
where
  carrier_serial_num =':carrier'
order by carrier_site