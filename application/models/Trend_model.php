<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trend_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get last 7 days aggregated values for a metric.
     * Returns array of ['date' => 'YYYY-MM-DD', 'value' => int]
     */
    public function get_weekly($metric = 'orders')
    {
        $sql = "SELECT DATE(created_at) AS day, SUM(value) AS total
                FROM weekly_trends
                WHERE metric = ? AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                GROUP BY DATE(created_at)
                ORDER BY DATE(created_at) ASC";

        $rows = $this->db->query($sql, [$metric])->result_array();

        // Build associative map day->total
        $map = [];
        foreach ($rows as $r) {
            $map[$r['day']] = (int)$r['total'];
        }

        // Ensure we return all 7 days (including today) with zero for missing days
        $out = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $out[] = ['date' => $d, 'value' => isset($map[$d]) ? $map[$d] : 0];
        }

        return $out;
    }

    /**
     * Get last 12 months aggregated values for a metric.
     * Returns array of ['month' => 'YYYY-MM', 'value' => int]
     */
    public function get_monthly($metric = 'orders')
    {
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(value) AS total
                FROM weekly_trends
                WHERE metric = ? AND DATE_FORMAT(created_at, '%Y-%m') >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), '%Y-%m')
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY DATE_FORMAT(created_at, '%Y-%m') ASC";

        $rows = $this->db->query($sql, [$metric])->result_array();
        $map = [];
        foreach ($rows as $r) {
            $map[$r['month']] = (int)$r['total'];
        }

        $out = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = date('Y-m', strtotime("-{$i} months"));
            $out[] = ['month' => $d, 'value' => isset($map[$d]) ? $map[$d] : 0];
        }
        return $out;
    }

    /**
     * Get total sum for a metric (all time)
     */
    public function get_total($metric = 'orders')
    {
        $sql = "SELECT SUM(value) AS total FROM weekly_trends WHERE metric = ?";
        $row = $this->db->query($sql, [$metric])->row_array();
        return isset($row['total']) ? (int)$row['total'] : 0;
    }
}
