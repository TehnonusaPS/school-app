export const getPhotoUrl = (filename) => {
  const base = import.meta.env.VITE_API_BASE_URL ?? '';
  if (!filename || typeof filename !== 'string') {
    return `${base}/assets/default-avatar.png`;
  }
  if (filename.startsWith('http://') || filename.startsWith('https://')) {
    return filename;
  }
  const normalizedBase = base.endsWith('/') ? base.slice(0, -1) : base;
  // return `${normalizedBase}/storage/student_faces/${filename}`;
  return `${normalizedBase}/storage/photos/${filename}`;
};
