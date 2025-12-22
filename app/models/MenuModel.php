<?php
namespace App\Models;

use App\Core\Model;

class MenuModel extends Model {
    protected $table = 'menu_items';
    
    /**
     * Get menu items for a specific role with self-referencing structure
     * @param string $role User role (admin, instructor, student, or null for public)
     * @return array Hierarchical menu structure
     */
    public function getMenuByRole($role = null) {
        $conditions = ['status' => 'active'];
        
        // If role is specified, get items for that role or public items
        if ($role) {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE status = 'active' 
                    AND (role = :role OR role = 'public' OR role IS NULL)
                    ORDER BY parent_id ASC, sort_order ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':role' => $role]);
        } else {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE status = 'active' 
                    AND (role = 'public' OR role IS NULL)
                    ORDER BY parent_id ASC, sort_order ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        
        $items = $stmt->fetchAll();
        
        // Build hierarchical structure
        return $this->buildMenuTree($items);
    }
    
    /**
     * Build hierarchical menu tree from flat array
     * @param array $items Flat array of menu items
     * @param int|null $parentId Parent ID to start from
     * @return array Hierarchical menu structure
     */
    private function buildMenuTree($items, $parentId = null) {
        $tree = [];
        
        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $children = $this->buildMenuTree($items, $item['id']);
                if (!empty($children)) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }
        
        return $tree;
    }
    
    /**
     * Get all menu items (for admin management)
     */
    public function getAllMenuItems() {
        return $this->findAll([], 'parent_id ASC, sort_order ASC');
    }
    
    /**
     * Create a new menu item
     */
    public function createMenuItem($data) {
        return $this->create($data);
    }
    
    /**
     * Update menu item
     */
    public function updateMenuItem($id, $data) {
        return $this->update($id, $data);
    }
    
    /**
     * Delete menu item and its children
     */
    public function deleteMenuItem($id) {
        // First delete children
        $children = $this->findAll(['parent_id' => $id]);
        foreach ($children as $child) {
            $this->deleteMenuItem($child['id']);
        }
        // Then delete the item itself
        return $this->delete($id);
    }
}








