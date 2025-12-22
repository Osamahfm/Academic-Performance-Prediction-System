<?php
namespace App\Core\Factory;

use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\CourseModel;
use App\Models\GradeModel;
use App\Models\ContactModel;
use App\Models\MenuModel;

/**
 * Factory Pattern - Model Factory
 * Creates model instances based on entity type
 */
class ModelFactory {
    private static $instances = [];
    
    /**
     * Create a model instance
     * @param string $type Model type (user, student, course, grade, contact, menu)
     * @return object Model instance
     * @throws \Exception If model type is invalid
     */
    public static function create($type) {
        $type = strtolower($type);
        
        // Return cached instance if exists (singleton-like behavior)
        if (isset(self::$instances[$type])) {
            return self::$instances[$type];
        }
        
        switch ($type) {
            case 'user':
                $model = new UserModel();
                break;
            case 'student':
                $model = new StudentModel();
                break;
            case 'course':
                $model = new CourseModel();
                break;
            case 'grade':
                $model = new GradeModel();
                break;
            case 'contact':
                $model = new ContactModel();
                break;
            case 'menu':
                $model = new MenuModel();
                break;
            default:
                throw new \Exception("Invalid model type: {$type}");
        }
        
        // Cache the instance
        self::$instances[$type] = $model;
        
        return $model;
    }
    
    /**
     * Clear cached instances (useful for testing)
     */
    public static function clearCache() {
        self::$instances = [];
    }
}








