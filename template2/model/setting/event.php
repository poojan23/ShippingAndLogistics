<?php

class ModelSettingEvent extends PT_Model
{
    public function getEvents()
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "event` WHERE `trigger` LIKE 'template/%' AND `status` = '1' ORDER BY `sort_order` ASC");

        return $query->rows;
    }
}
