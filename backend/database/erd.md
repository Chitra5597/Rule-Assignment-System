## Rule Assignment System - ERD   

    ┌─────────────┐                    ┌─────────────┐
    │    RULES    │                    │   GROUPS    │
    ├─────────────┤                    ├─────────────┤
    │ id (PK)     │                    │ id (PK)     │
    │ name (UK)   │                    │ name (UK)   │
    │ type        │                    │ created_at  │
    │ created_at  │                    └─────────────┘
    └──────┬──────┘                            │
           │ 1:N                               │ 1:N
           │                                  │
           └──────────────────┬───────────────┘
                              │
                    ┌─────────▼──────────┐
                    │   ASSIGNMENTS     │
                    ├───────────────────┤
                    │ id (PK)           │
                    │ group_id (FK)     │
                    │ rule_id (FK)      │
                    │ parent_id (FK) ◄──┼─ Self-Ref (1:N)
                    │ level (1-3)       │
                    │ order_num         │
                    │ created_at        │
                    └───────────────────┘



## Constraints:
- RULES.name is UNIQUE
- GROUPS.name is UNIQUE
- ASSIGNMENTS.parent_id can be NULL (root level)
- Max tier_level = 3
- Self-referencing FK for parent-child hierarchy