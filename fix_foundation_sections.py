import os
import re

filepath = "/Users/am/Jobs/school-app/frontend/src/modules/lainnya/pages/konfigurasi-global/IndexPage.vue"

with open(filepath, 'r') as f:
    content = f.read()

# 1. Update createFoundationSections
old_func = """const createFoundationSections = () => [
  { id: 1, type: 'stats', title: 'Jejaring Yayasan', is_visible: true, sort_order: 1, items: [
    { id: 11, title: 'Lembaga Pendidikan', value: '12' },
    { id: 12, title: 'Total Siswa/Santri', value: '5,400+' },
    { id: 13, title: 'Alumni Tersebar', value: '15,000+' },
    { id: 14, title: 'Pondok Pesantren', value: '3' }
  ] },
  { id: 2, type: 'features', title: 'Fokus Yayasan Kami', is_visible: true, sort_order: 2, items: [
    { id: 21, title: 'Pendidikan Inklusif', description: 'Membangun lembaga yang dapat diakses oleh seluruh lapisan masyarakat.', icon: 'award' },
    { id: 22, title: 'Pemberdayaan Umat', description: 'Menyelenggarakan program beasiswa dan bantuan pendidikan bagi yatim dhuafa.', icon: 'heart' },
    { id: 23, title: 'Jejaring Global', description: 'Berkomitmen mendidik dengan kurikulum yang diakui secara internasional.', icon: 'book' }
  ] },
  { id: 3, type: 'programs', title: 'Lembaga Pendidikan', is_visible: true, sort_order: 3, items: [
    { id: 31, title: 'Pondok Pesantren', description: 'Pusat pendidikan agama dan pengkajian kitab kuning.', image: 'https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=600' },
    { id: 32, title: 'Sekolah Terpadu', description: 'Pendidikan formal dari tingkat TK hingga SMA.', image: 'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=600' },
    { id: 33, title: 'Pusat Diklat Terpadu', description: 'Lembaga pengembangan SDM untuk mencetak profesional tangguh.', image: 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=600' }
  ] },"""

new_func = """const createFoundationSections = (schools = []) => {
  const images = [
    'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=600',
    'https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=600',
    'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=600',
    'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=600'
  ];
  
  const programItems = schools.length > 0 
    ? schools.map((s, idx) => ({
        id: 31 + idx,
        title: s.name,
        description: s.level ? `Pendidikan tingkat ${s.level}.` : 'Lembaga pendidikan unggulan.',
        image: images[idx % images.length]
      }))
    : [
        { id: 31, title: 'Pondok Pesantren', description: 'Pusat pendidikan agama dan pengkajian kitab kuning.', image: images[1] },
        { id: 32, title: 'Sekolah Terpadu', description: 'Pendidikan formal dari tingkat TK hingga SMA.', image: images[0] },
        { id: 33, title: 'Pusat Diklat Terpadu', description: 'Lembaga pengembangan SDM untuk mencetak profesional tangguh.', image: images[2] }
      ];

  return [
  { id: 1, type: 'stats', title: 'Jejaring Yayasan', is_visible: true, sort_order: 1, items: [
    { id: 11, title: 'Lembaga Pendidikan', value: schools.length > 0 ? schools.length.toString() : '12' },
    { id: 12, title: 'Total Siswa/Santri', value: '5,400+' },
    { id: 13, title: 'Alumni Tersebar', value: '15,000+' },
    { id: 14, title: 'Pondok Pesantren', value: '3' }
  ] },
  { id: 2, type: 'features', title: 'Fokus Yayasan Kami', is_visible: true, sort_order: 2, items: [
    { id: 21, title: 'Pendidikan Inklusif', description: 'Membangun lembaga yang dapat diakses oleh seluruh lapisan masyarakat.', icon: 'award' },
    { id: 22, title: 'Pemberdayaan Umat', description: 'Menyelenggarakan program beasiswa dan bantuan pendidikan bagi yatim dhuafa.', icon: 'heart' },
    { id: 23, title: 'Jejaring Global', description: 'Berkomitmen mendidik dengan kurikulum yang diakui secara internasional.', icon: 'book' }
  ] },
  { id: 3, type: 'programs', title: 'Lembaga Pendidikan', is_visible: true, sort_order: 3, items: programItems },"""

content = content.replace(old_func, new_func)

# Fix array closure yang hilang
# Pada file asli, createFoundationSections diakhiri dengan } tanpa ] karena itu arrow function tanpa kurung kurawal
# Karena sekarang memakai { dan return [, kita harus menutupnya dengan ]; } 
content = content.replace("  ] }\n]", "  ] }\n]") # wait let's use robust regex

# Menyesuaikan tutup fungsi
content = re.sub(
    r"(id: 6, type: 'faq', title: 'FAQ Yayasan', is_visible: true, sort_order: 6, items: \[.*?\] \}\n)\]",
    r"\1];\n}",
    content,
    flags=re.DOTALL
)

# 2. Update fetchConfigData agar passing schools:
content = content.replace(
    "sections: config.sections || createFoundationSections()",
    "schools: f.schools || [],\n        sections: config.sections || createFoundationSections(f.schools || [])"
)

# 3. Update openModalEditor agar passing item.schools:
content = content.replace(
    "item.sections || (activeTab.value === 'sekolah' ? createSchoolSections() : createFoundationSections())",
    "item.sections || (activeTab.value === 'sekolah' ? createSchoolSections() : createFoundationSections(item.schools || []))"
)

# Helper createDefaultEntity fixing if needed:
content = content.replace(
    "sections: type === 'Yayasan' ? createFoundationSections() : createSchoolSections()",
    "sections: type === 'Yayasan' ? createFoundationSections([]) : createSchoolSections()"
)

with open(filepath, 'w') as f:
    f.write(content)

print("Replacement script executed")
