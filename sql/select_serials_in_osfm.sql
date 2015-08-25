select 
  ORGANIZATION_CODE,
  DEPARTMENT_CODE,
  ITEM,
  ITEM_DESCRIPTION,
  JOB,
  to_Char(DATE_RECEIVED,'yyyy-mm-dd hh24:mi')DATE_RECEIVED,
  ONHAND_QTY,
  SUBINVENTORY_CODE,
  UOM,
  SAP_PART_NO,
  COMCODE,
  LOCATOR_ID,
  LPN,
  AGED_DAYS,
  COST_GROUP
from
  apps.xxbi_cyp_onhand_inv_v@osfm
where
  organization_code = 'F07' and 
  department_code = 'PIC-LR4' and 
  job in (:serials)