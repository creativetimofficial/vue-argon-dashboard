-- Activate all users who have verified their email but are still inactive
UPDATE users 
SET is_active = 1 
WHERE email_verified_at IS NOT NULL 
AND is_active = 0;

-- Activate all ISPs that have verified users
UPDATE isps 
SET is_active = 1 
WHERE id IN (
    SELECT DISTINCT isp_id 
    FROM users 
    WHERE email_verified_at IS NOT NULL 
    AND is_active = 1 
    AND isp_id IS NOT NULL
);
