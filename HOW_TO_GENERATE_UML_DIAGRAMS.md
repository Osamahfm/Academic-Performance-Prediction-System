# How to Generate UML Diagrams

## Option 1: Send to ChatGPT

### Step 1: Copy the Prompt
Open `chatgpt-uml-prompt.txt` and copy the entire content.

### Step 2: Send to ChatGPT
1. Go to ChatGPT (chat.openai.com)
2. Paste the prompt from `chatgpt-uml-prompt.txt`
3. Ask ChatGPT to generate the PlantUML code for all diagrams

### Step 3: Use the Generated Code
- ChatGPT will provide PlantUML code
- Copy each diagram code
- Go to http://www.plantuml.com/plantuml/uml/
- Paste the code and click "Submit" to generate the image
- Right-click the image to save it

---

## Option 2: Use Existing PlantUML Code

### Step 1: Open the UML File
The file `uml.txt` contains all PlantUML diagrams ready to use.

### Step 2: Extract Individual Diagrams
Each diagram is between `@startuml DiagramName` and `@enduml`.

### Step 3: Generate Images Online
1. Go to **http://www.plantuml.com/plantuml/uml/**
2. Copy one diagram code from `uml.txt` (from @startuml to @enduml)
3. Paste it into the online editor
4. Click "Submit" to generate the image
5. Right-click to save as PNG or SVG

---

## Option 3: Use PlantUML Server API

### Generate Images Programmatically

You can use PlantUML's server to generate images directly:

```
http://www.plantuml.com/plantuml/img/[encoded_diagram]
```

Or use the SVG format:
```
http://www.plantuml.com/plantuml/svg/[encoded_diagram]
```

---

## Available Diagrams

1. **Context_Diagram** - System context showing actors and external systems
2. **Use_Case_Diagram** - All 33 use cases organized by packages
3. **Sequence_Predict_Performance** - Flow for prediction feature
4. **Sequence_Login** - Authentication flow
5. **Sequence_Alert_Creation** - Alert generation flow
6. **Class_Diagram** - All classes and relationships
7. **State_Diagram** - Student risk level management states
8. **State_Diagram_User_Session** - User session management states

---

## Quick ChatGPT Prompt

If you want a shorter prompt, use this:

```
Generate PlantUML UML diagrams for an EduPredict academic performance prediction system. 
Include: Context Diagram, Use Case Diagram (33 use cases), 3 Sequence Diagrams (Predict Performance, Login, Alert Creation), 
Class Diagram (MVC architecture with Controllers, Models, Services, ML components), and 2 State Diagrams (Risk Management, Session Management).
The system uses KNN machine learning, has role-based access (Admin/Instructor/Student), and MySQL database.
```

---

## Tips

- **For better diagrams**: Ask ChatGPT to improve the diagrams with better styling
- **For documentation**: Ask ChatGPT to add descriptions and notes to diagrams
- **For export**: Use PlantUML's export features to get PDF, PNG, or SVG formats
- **For editing**: Use PlantUML syntax to customize colors, fonts, and layouts

---

## PlantUML Syntax Reference

- `@startuml` / `@enduml` - Start/end diagram
- `!theme plain` - Set theme
- `actor` - Define actor
- `participant` - Define participant in sequence
- `class` - Define class
- `package` - Group elements
- `-->` - Arrow/relationship
- `note right/left` - Add notes

For more syntax: https://plantuml.com/


