-- Demo users: passwords are Admin@123
INSERT INTO users (full_name, email, password_hash, role) VALUES
('Dr. Sarah Smith','dr_smith@example.com', '$2y$10$0LfT98V4bm0Z2g5g3/ye6O6gD3cDxxC7bJUkFVJ0Sx3H9sB0e3DVS', 'shopowner'),
('John Doe','john_doe@example.com', '$2y$10$0LfT98V4bm0Z2g5g3/ye6O6gD3cDxxC7bJUkFVJ0Sx3H9sB0e3DVS', 'customer');

-- Demo products owned by Dr. Smith (owner_id = 1)
INSERT INTO products (owner_id, name, description, price, stock, category, image_url) VALUES
(1,'ECG Monitor','12-lead portable ECG monitor with display', 1299.00, 10,'Diagnostic','https://images.unsplash.com/photo-1584982751601-97dcc097e19f?q=80&w=2069&auto=format&fit=crop'),
(1,'Infusion Pump','Smart infusion pump with drug library', 899.00, 12, 'Biomedical','https://images.unsplash.com/photo-1582719508461-905c673771fd?q=80&w=1965&auto=format&fit=crop'),
(1,'Hospital Bed','Electric ICU bed with side rails', 2199.00, 5, 'Durable','https://images.unsplash.com/photo-1579684453423-95eec0e3c8d7?q=80&w=2100&auto=format&fit=crop'),
(1,'AED Defibrillator','Public-access AED with voice prompts', 699.00, 20, 'Defibrillators','https://images.unsplash.com/photo-1585637071669-7c1e1cca9c6f?q=80&w=2000&auto=format&fit=crop');
