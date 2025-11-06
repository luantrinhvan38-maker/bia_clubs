-- 1. Tables 
CREATE TABLE Tables ( 
  TableID INT PRIMARY KEY AUTO_INCREMENT, 
  TableName VARCHAR(50) UNIQUE NOT NULL, 
  Status ENUM('Available','Playing','Maintenance') DEFAULT 'Available', 
  HourlyRate DECIMAL(10,2) NOT NULL, 
  Description TEXT 
) ENGINE=InnoDB; 

-- 2. Services 
CREATE TABLE Services ( 
  ServiceID INT PRIMARY KEY AUTO_INCREMENT, 
  ServiceName VARCHAR(100) NOT NULL, 
  Price_Service DECIMAL(10,2) NOT NULL, 
  Category ENUM('Drink','Food','Snack') NOT NULL, 
  Numbers INT DEFAULT NULL, 
  INDEX idx_name (ServiceName) 
) ENGINE=InnoDB; 

-- 3. Invoices 
CREATE TABLE Invoices ( 
  InvoiceID INT PRIMARY KEY AUTO_INCREMENT, 
  TableID INT NOT NULL, 
  StartTime DATETIME NULL, 
  EndTime DATETIME NULL, 
  TimePlay DECIMAL(5,2) NULL, 
  InvoiceDate DATETIME DEFAULT CURRENT_TIMESTAMP, 
  TotalAmount DECIMAL(10,2) NOT NULL, 
  PaymentMethod ENUM('Cash','Card','Other') DEFAULT 'Cash', 
  IsPaid BOOLEAN DEFAULT FALSE, 
  FOREIGN KEY (TableID) REFERENCES Tables(TableID) ON DELETE CASCADE 
) ENGINE=InnoDB; 

-- 4. InvoiceDetails 
CREATE TABLE InvoiceDetails ( 
  InvoiceDetailID INT PRIMARY KEY AUTO_INCREMENT, 
  InvoiceID INT NOT NULL, 
  TableID INT NOT NULL, 
  ServiceID INT NULL, 
  Numbers INT NULL, 
  Price_Services DECIMAL(12,2) NOT NULL, 
  Note VARCHAR(255) NULL, 
  FOREIGN KEY (InvoiceID) REFERENCES Invoices(InvoiceID) ON DELETE CASCADE, 
  FOREIGN KEY (TableID) REFERENCES Tables(TableID) ON DELETE CASCADE, 
  FOREIGN KEY (ServiceID) REFERENCES Services(ServiceID) ON DELETE SET NULL, 
  INDEX idx_invoice (InvoiceID), 
  INDEX idx_service (ServiceID) 
) ENGINE=InnoDB;