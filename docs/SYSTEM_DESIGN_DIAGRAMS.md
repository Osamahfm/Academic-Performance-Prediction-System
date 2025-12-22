# EduPredict System Design Diagrams

## System Overview
EduPredict is an academic performance prediction system using KNN machine learning to predict student GPA and identify at-risk students. The system follows MVC architecture with role-based access control (Admin, Instructor, Student).

---

## 1. Context Diagram (Level 0 DFD)

```mermaid
flowchart TD
    Admin[Administrator]
    Instructor[Instructor]
    Student[Student]
    System[EduPredict System]
    Database[(MySQL Database)]
    
    Admin -->|Manage Users/Courses| System
    Admin -->|View Reports| System
    Admin -->|Train ML Model| System
    
    Instructor -->|Manage Grades| System
    Instructor -->|View Student Performance| System
    Instructor -->|Create Courses| System
    
    Student -->|View Dashboard| System
    Student -->|View Predictions| System
    Student -->|View Grades| System
    
    System -->|Store/Retrieve Data| Database
    System -->|Generate Predictions| System
    
    style System fill:#2c5aa0,stroke:#1e3a8a,color:#fff
    style Database fill:#28a745,stroke:#20c997,color:#fff
```

---

## 2. Use Case Diagram

```mermaid
graph TB
    subgraph "EduPredict System"
        UC1[Login]
        UC2[Register]
        UC3[Logout]
        UC4[View Dashboard]
        UC5[Manage Users]
        UC6[Manage Courses]
        UC7[Manage Students]
        UC8[Assign Grades]
        UC9[View Grades]
        UC10[Generate Predictions]
        UC11[Train KNN Model]
        UC12[View Predictions]
        UC13[View Risk Alerts]
        UC14[Manage Alerts]
        UC15[View Reports]
        UC16[Manage Menu]
    end
    
    Admin[Administrator]
    Instructor[Instructor]
    Student[Student]
    
    Admin --> UC1
    Admin --> UC2
    Admin --> UC3
    Admin --> UC4
    Admin --> UC5
    Admin --> UC6
    Admin --> UC7
    Admin --> UC11
    Admin --> UC15
    Admin --> UC16
    
    Instructor --> UC1
    Instructor --> UC2
    Instructor --> UC3
    Instructor --> UC4
    Instructor --> UC6
    Instructor --> UC8
    Instructor --> UC9
    Instructor --> UC10
    Instructor --> UC12
    Instructor --> UC13
    Instructor --> UC14
    
    Student --> UC1
    Student --> UC2
    Student --> UC3
    Student --> UC4
    Student --> UC9
    Student --> UC12
    
    style Admin fill:#dc3545,stroke:#c82333,color:#fff
    style Instructor fill:#ffc107,stroke:#ff9800,color:#000
    style Student fill:#28a745,stroke:#20c997,color:#fff
```

---

## 3. Sequence Diagram - Generate Student Prediction (Critical Use Case)

```mermaid
sequenceDiagram
    actor Student
    participant Frontend as Frontend View
    participant Router as Router
    participant PC as PredictionController
    participant PS as PredictionService
    participant KNN as KNNPredictor
    participant SM as StudentModel
    participant GM as GradeModel
    participant PM as PredictionModel
    participant DB as Database
    
    Student->>Frontend: Request Predictions Page
    Frontend->>Router: GET /predictions
    Router->>PC: index()
    
    activate PC
    PC->>SM: findById(studentId)
    SM->>DB: SELECT * FROM students
    DB-->>SM: Student Data
    SM-->>PC: Student Object
    
    PC->>PS: predictPerformance(studentId)
    activate PS
    
    PS->>GM: getGradesByStudent(studentId)
    GM->>DB: SELECT * FROM grades WHERE student_id=?
    DB-->>GM: Grades Data
    GM-->>PS: Grades Array
    
    PS->>SM: findById(studentId)
    SM->>DB: SELECT * FROM students WHERE id=?
    DB-->>SM: Student Data
    SM-->>PS: Student Features
    
    PS->>PS: prepareTrainingData()
    PS->>DB: SELECT students with grades
    DB-->>PS: Training Data
    
    PS->>KNN: loadTrainingData(data)
    activate KNN
    KNN-->>PS: Training Complete
    
    PS->>KNN: predict(features)
    KNN->>KNN: Calculate Distances
    KNN->>KNN: Find K Nearest Neighbors
    KNN->>KNN: Determine Risk Level
    KNN-->>PS: Prediction Result
    
    deactivate KNN
    
    PS->>PS: calculateCurrentGpa(studentId)
    PS->>PS: calculatePredictedGpa(grades)
    PS->>PS: identifyRiskFactors(features)
    
    PS->>PM: savePrediction(studentId, courseId, data)
    PM->>DB: INSERT INTO predictions
    DB-->>PM: Prediction ID
    PM-->>PS: Success
    
    PS-->>PC: Prediction Results
    deactivate PS
    
    PC->>PM: getPredictionsByStudent(studentId)
    PM->>DB: SELECT * FROM predictions WHERE student_id=?
    DB-->>PM: Predictions Data
    PM-->>PC: Predictions Array
    
    PC->>Frontend: Render View with Data
    deactivate PC
    Frontend-->>Student: Display Predictions Page
```

---

## 4. Class Diagram

```mermaid
classDiagram
    class Database {
        -static $instance: Database
        -$connection: PDO
        +getInstance() Database
        +getConnection() PDO
    }
    
    class Router {
        -$routes: array
        +dispatch() void
        -matchRoute() array
    }
    
    class Controller {
        <<abstract>>
        +view() void
        +requireRole() void
        +requireAuth() void
    }
    
    class AuthController {
        -userModel: UserModel
        +login() void
        +register() void
        +logout() void
    }
    
    class PredictionController {
        -predictionService: PredictionService
        -predictionModel: PredictionModel
        +index() void
        +train() void
        +course() void
    }
    
    class DashboardController {
        -studentModel: StudentModel
        -gradeModel: GradeModel
        +admin() void
        +instructor() void
        +student() void
    }
    
    class CrudController {
        -modelFactory: ModelFactory
        -validationStrategy: ValidationStrategy
        +index() void
        +create() void
        +update() void
        +delete() void
    }
    
    class UserModel {
        -table: string
        -db: PDO
        +findByEmail() array
        +createUser() int
        +verifyPassword() bool
    }
    
    class StudentModel {
        -table: string
        -db: PDO
        +findById() array
        +findByUserId() array
        +updateRiskLevel() int
        +getAtRiskStudents() array
    }
    
    class GradeModel {
        -table: string
        -db: PDO
        +getGradesByStudent() array
        +getGradesByCourse() array
        +getAverageGrade() float
        +triggerPrediction() void
    }
    
    class PredictionModel {
        -table: string
        -db: PDO
        +savePrediction() int
        +getPredictionsByStudent() array
        +getLatestPrediction() array
    }
    
    class PredictionService {
        -knnPredictor: KNNPredictor
        -studentModel: StudentModel
        -gradeModel: GradeModel
        -predictionModel: PredictionModel
        +predictPerformance() array
        -prepareTrainingData() array
        -getStudentFeatures() array
        -identifyRiskFactors() array
        -calculateCurrentGpa() float
        -gradeToGpa() float
    }
    
    class KNNPredictor {
        -k: int
        -trainingData: array
        +loadTrainingData() void
        +predict() array
        -euclideanDistance() float
        -normalizeFeatures() array
    }
    
    class ModelFactory {
        <<singleton>>
        -static $instances: array
        +create() Model
    }
    
    class ValidationStrategy {
        <<interface>>
        +validate() bool
        +getErrors() array
    }
    
    class UserValidationStrategy {
        +validate() bool
        +getErrors() array
    }
    
    class CourseValidationStrategy {
        +validate() bool
        +getErrors() array
    }
    
    Controller <|-- AuthController
    Controller <|-- PredictionController
    Controller <|-- DashboardController
    Controller <|-- CrudController
    
    PredictionController --> PredictionService
    PredictionController --> PredictionModel
    
    PredictionService --> KNNPredictor
    PredictionService --> StudentModel
    PredictionService --> GradeModel
    PredictionService --> PredictionModel
    
    AuthController --> UserModel
    DashboardController --> StudentModel
    DashboardController --> GradeModel
    
    CrudController --> ModelFactory
    CrudController --> ValidationStrategy
    
    ValidationStrategy <|.. UserValidationStrategy
    ValidationStrategy <|.. CourseValidationStrategy
    
    UserModel --> Database
    StudentModel --> Database
    GradeModel --> Database
    PredictionModel --> Database
    
    Database ..> Database : Singleton
    ModelFactory ..> ModelFactory : Singleton
```

---

## 5. State Diagram - Student Prediction Lifecycle

```mermaid
stateDiagram-v2
    [*] --> NoData: Student Created
    
    NoData --> DataCollection: Grades Assigned
    NoData --> Training: Admin Trains Model
    
    DataCollection --> InsufficientData: < 3 Grades
    DataCollection --> ReadyForPrediction: >= 3 Grades
    
    InsufficientData --> ReadyForPrediction: More Grades Added
    
    ReadyForPrediction --> Calculating: Prediction Requested
    
    Calculating --> LowRisk: GPA >= 3.0
    Calculating --> MediumRisk: 2.0 <= GPA < 3.0
    Calculating --> HighRisk: GPA < 2.0
    
    LowRisk --> Stable: Trend = Stable
    LowRisk --> Increasing: Trend = Increase
    LowRisk --> Decreasing: Trend = Decrease
    
    MediumRisk --> Stable: Trend = Stable
    MediumRisk --> Increasing: Trend = Increase
    MediumRisk --> Decreasing: Trend = Decrease
    
    HighRisk --> AlertGenerated: Risk Level = High
    HighRisk --> Monitoring: Risk Level < High
    
    AlertGenerated --> Monitoring: Alert Created
    
    Stable --> Updating: New Grade Added
    Increasing --> Updating: New Grade Added
    Decreasing --> Updating: New Grade Added
    Monitoring --> Updating: New Grade Added
    
    Updating --> Calculating: Recalculate Prediction
    
    Training --> ReadyForPrediction: Training Complete
    
    note right of NoData
        Student has no grades
        or predictions yet
    end note
    
    note right of Calculating
        KNN Algorithm:
        - Load training data
        - Calculate features
        - Find k nearest neighbors
        - Determine risk level
    end note
    
    note right of AlertGenerated
        System creates alert
        for instructor/admin
    end note
```

---

## Diagram Notes

### Context Diagram
- Shows external entities (Admin, Instructor, Student) interacting with the EduPredict system
- Database is shown as a data store
- System acts as central process handling all interactions

### Use Case Diagram
- Groups use cases within system boundary
- Shows role-based access (different actors have different permissions)
- Critical use cases: Prediction generation, KNN training, Grade management

### Sequence Diagram
- Most critical use case: Generate Student Prediction
- Shows complete flow from user request to database operations
- Includes ML prediction process using KNN algorithm
- Demonstrates MVC pattern interactions

### Class Diagram
- Shows OOP structure with inheritance (Controller hierarchy)
- Demonstrates design patterns:
  - Singleton: Database, ModelFactory
  - Strategy: ValidationStrategy implementations
  - Factory: ModelFactory
- Shows relationships: composition, association, inheritance

### State Diagram
- Student Prediction lifecycle from creation to monitoring
- States represent prediction status and risk levels
- Transitions triggered by events (grade assignment, prediction request)
- Includes alert generation for high-risk students

---

## Technical Architecture Summary

**Patterns Used:**
- MVC (Model-View-Controller)
- Singleton (Database connection)
- Factory (Model creation)
- Strategy (Validation rules)

**Key Technologies:**
- PHP 8.2+ (Backend)
- MySQL 8.0+ (Database)
- KNN Algorithm (Machine Learning)
- PDO (Database Access)

**Security:**
- Role-based access control
- Password hashing
- Session management
- Input validation (client & server-side)




