select 
  job,
  subinventory_code,
  item,
  aged_days
from
  apps.xxbi_cyp_onhand_inv_v@osfm
where
  job in (:serials)