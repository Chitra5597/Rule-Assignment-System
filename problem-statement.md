## 1. Introduction
This exercise involves working with a rule assignment system in which predefined rules are organised into groups. The objective is to model, store, and present hierarchical rule structures
in a clear and logical way.
- A rule contains: Rule ID, Rule Name, Rule Type (Condition or Decision)
- A group contains: Group ID, Group Name

### Rule Types:
- Condition Rule: Represents a conditional branch and must have at least one child rule.
- Decision Rule: Represents an end decision or action and cannot have any child rules.

### Hierarchy Constraints:
- Rules are structured in a parent - child relationship.
- A maximum of 3 tiers is allowed.
- Groups may contain multiple rules in each tier.
- Rules can be reused in di􀆯erent tiers but not under the same parent node (including root
node).

## 2. Rule Assignment Examples

**Group A** 
| -- Tier 1: Decision Rule 1   
| -- Tier 1: Condition Rule 1  
| -- Tier 2: Decision Rule 1  
| -- Tier 2: Decision Rule 2

**Group B**  
| -- Tier 1: Condition Rule 1  
| -- Tier 2: Decision Rule 1  
| -- Tier 2: Condition Rule 2  
| -- Tier 3: Decision Rule 2  
| -- Tier 1: Decision Rule 3  

## 3. Tasks
### 3.1 Database Design
Design a relational database schema to store rules, groups, and hierarchical assignments.
The design should clearly define:
- Core entities (Rules, Groups, Assignments)
- Parent–child rule relationships
- Tier-level handling and constraints

### 3.2 Web Interface Development
Develop a web interface enabling users to:
- Create a new group and assign rules to it
- View a group and its associated rules in the order they were assigned
- Edit the rule assignments for an existing group and save to view
Additional considerations:
- The interface must display rules in hierarchical order.
- The system must enforce tier limits and rule constraints.
- Validation must prevent incorrect assignments.

## 4. Expectations
- You are expected to complete this exercise on your own.
- The solution must be developed using object-oriented PHP (8.0 and above) programming.
- The solution must include SQL scripts to create the tables in your database design. You may include an ERD or Schema diagram, but this is not essential.
- Do not use third-party frameworks such as Laravel, Phalcon, etc for this exercise. You may use Vue.js or jQuery for the User Interface.
- Prepare a report to explain your solution.
- While not essential, including unit tests is a bonus.
  
