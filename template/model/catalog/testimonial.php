<?php

class ModelCatalogTestimonial extends PT_Model
{
    public function getTestimonials($start = 0, $limit = 20)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 20;
        }

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "testimonial WHERE status = '1' AND date_added <= NOW() ORDER BY sort_order LIMIT " . (int)$start . "," . (int)$limit);

        return $query->rows;
    }
}
