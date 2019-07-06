<?php

class ModelClubAddCitation extends PT_Model {

  public function getCitationTableForm()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "dgcitation");

        return $query->rows;
    }
}
