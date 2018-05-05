RENAME TABLE `#__supportgroups_capacity_type` TO `#__supportgroups_info_type`;

ALTER TABLE `#__supportgroups_support_group` CHANGE `capacity` `info` TEXT NOT NULL DEFAULT '';
