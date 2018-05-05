SELECT
  YEAR(time), MONTH(time), DAY(time),
  AVG(down) AS avg_down, MAX(down) AS max_down, MIN(down) AS min_down
FROM results GROUP BY YEAR(time), MONTH(time), DAY(time);
