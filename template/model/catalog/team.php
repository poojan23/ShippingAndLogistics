<?php

class ModelCatalogTeam extends PT_Model
{
    public function getTeams($start, $limit)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 20;
        }

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "team WHERE status = '1' ORDER BY sort_order LIMIT " . (int)$start . "," . (int)$limit);

        return $query->rows;
    }
}
