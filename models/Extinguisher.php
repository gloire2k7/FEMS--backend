<?php

class Extinguisher extends Model
{
    protected $table = 'fire_extinguishers';

    public function create($data)
    {
        $query = "INSERT INTO " . $this->table . " (serial_number, qr_code_path, type, capacity, status, manufacturing_date, filling_date, expiry_date, client_id, location_id) VALUES (:serial_number, :qr_code_path, :type, :capacity, :status, :manufacturing_date, :filling_date, :expiry_date, :client_id, :location_id)";
        $stmt = $this->db->prepare($query);

        $status = isset($data['status']) ? $data['status'] : 'filled';

        $stmt->bindParam(':serial_number', $data['serial_number']);
        $stmt->bindParam(':qr_code_path', $data['qr_code_path']);
        $stmt->bindParam(':type', $data['type']);
        $stmt->bindParam(':capacity', $data['capacity']);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':manufacturing_date', $data['manufacturing_date']);
        $stmt->bindParam(':filling_date', $data['filling_date']);
        $stmt->bindParam(':expiry_date', $data['expiry_date']);
        $stmt->bindParam(':client_id', $data['client_id']);
        $stmt->bindParam(':location_id', $data['location_id']);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function updateStatus($id, $status)
    {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
