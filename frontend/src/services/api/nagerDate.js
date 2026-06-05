// Service to fetch Indonesian national holidays from Nager.Date API

const cache = new Map();

export async function fetchIndonesianHolidays(year) {
  if (cache.has(year)) {
    return cache.get(year);
  }

  try {
    const response = await fetch(`https://date.nager.at/api/v3/PublicHolidays/${year}/ID`);
    if (!response.ok) {
      throw new Error(`Failed to fetch holidays for year ${year}`);
    }
    const data = await response.json();
    
    // Map data to a simple structure
    const holidays = data.map(holiday => ({
      date: holiday.date, // format YYYY-MM-DD
      localName: holiday.localName,
      name: holiday.name
    }));

    cache.set(year, holidays);
    return holidays;
  } catch (error) {
    console.error('Error fetching national holidays:', error);
    // Return empty array or fall back to some static common holidays if API is down
    return [];
  }
}
