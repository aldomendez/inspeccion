Select 
  carrier_Serial_num,
  carrier_site,
  serial_num,
  Status
from
  phase2.carrier_site@mxoptix
where
  carrier_serial_num =':carrier'
order by carrier_site