// // ==========================
// // RATING STARS
// // ==========================
// // Kita bikin interaksi bintang supaya bisa klik dan menandai rating
// document.addEventListener('DOMContentLoaded', () => {
//     const stars = document.querySelectorAll('.star-rating input'); // ambil semua input radio
//     const labels = document.querySelectorAll('.star-rating label'); // ambil label yang diklik

//     // Loop tiap label, pas diklik, kita set star sesuai index
//     labels.forEach((label, idx) => {
//         label.addEventListener('click', () => {
//             // Reset semua bintang
//             stars.forEach(star => star.checked = false);
//             // Pilih bintang sesuai index (dari kanan ke kiri)
//             stars[stars.length - 1 - idx].checked = true;
//         });
//     });
// });

// // ==========================
// // HITUNG KATA & KARAKTER
// // ==========================
// // Ambil textarea dan elemen counter
// const textarea = document.getElementById('review_text');
// const counterWords = document.getElementById('counter_words');
// const counterChars = document.getElementById('counter_chars');

// // Fungsi update counter kata & karakter
// function updateCounter() {
//     let text = textarea.value;
//     let words = text.trim() === "" ? 0 : text.trim().split(/\s+/).length; // hitung kata
//     let chars = text.length; // hitung karakter

//     // Batasi kata maksimal 300
//     if (words > 300) {
//         let wordArr = text.trim().split(/\s+/).slice(0, 300);
//         textarea.value = wordArr.join(" ");
//         words = 300;
//     }

//     // Batasi karakter maksimal 1500
//     if (chars > 1500) {
//         textarea.value = textarea.value.substr(0, 1500);
//         chars = 1500;
//     }

//     // Tampilkan jumlah kata & karakter di halaman
//     counterWords.textContent = words;
//     counterChars.textContent = chars;
// }

// // Event listener untuk setiap input perubahan
// textarea.addEventListener('input', updateCounter);
// // Pastikan counter update saat halaman load
// window.addEventListener('load', updateCounter);

// // ==========================
// // PREVIEW MEDIA (IMAGE / VIDEO)
// // ==========================
// const mediaInput = document.querySelector('input[type="file"][data-preview]'); // input file dengan data-preview
// const previewElem = document.getElementById(mediaInput.dataset.preview); // elemen untuk preview

// mediaInput.addEventListener('change', () => {
//     previewElem.innerHTML = ''; // kosongkan preview lama
//     const files = mediaInput.files;

//     Array.from(files).forEach(file => {
//         // Batasi ukuran file maksimal 5MB
//         if (file.size > 5 * 1024 * 1024) {
//             alert('File terlalu besar! Maks 5MB 😭');
//             mediaInput.value = ''; // reset input
//             return;
//         }

//         const reader = new FileReader();
//         reader.onload = e => {
//             if (file.type.startsWith('image')) {
//                 // Kalau file gambar, buat tag <img>
//                 const img = document.createElement('img');
//                 img.src = e.target.result;
//                 img.style.maxWidth = '200px';
//                 img.style.margin = '5px';
//                 previewElem.appendChild(img);
//             } else if (file.type.startsWith('video')) {
//                 // Kalau file video, buat tag <video>
//                 const video = document.createElement('video');
//                 video.src = e.target.result;
//                 video.controls = true;
//                 video.width = 200;
//                 previewElem.appendChild(video);
//             }
//         };
//         reader.readAsDataURL(file); // baca file sebagai URL
//     });
// });
