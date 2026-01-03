import { differenceInYears } from 'date-fns';

export const yearsSince = (date) => {
  if (!date) return 0;
  return differenceInYears(new Date(), new Date(date));
};
