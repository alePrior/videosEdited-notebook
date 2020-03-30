-- check size of tables
SELECT table_schema AS "Database",
SUM(data_length + index_length) / 1024 / 1024 AS "Size (MB)"
FROM information_schema.TABLES
GROUP BY table_schema

--  check size and database availabe space
SELECT table_schema "dataBase scheme",
sum( data_length + index_length ) / 1024 / 1024 "Data Base Size in MB",
sum( data_free )/ 1024 / 1024 "Free Space in MB"
FROM information_schema.TABLES
GROUP BY table_schema;

-- for error 1175 (while updating multiple records)
SET SQL_SAFE_UPDATES = 0;