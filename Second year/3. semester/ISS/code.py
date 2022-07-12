#Signály a systémy
#David Kocman, xkocma08, 17.12.2021
#filtrace signálu

from scipy.io import wavfile
from scipy import signal
import numpy as np
import matplotlib.pyplot as plt
import librosa
import math
import wavio
import soundfile
from scipy.signal import spectrogram, lfilter, freqz, tf2zpk
# -------------------------------------------------------------------------
# 4.1 Zaklady
# -------------------------------------------------------------------------
fs, data = wavfile.read('xkocma08.wav')

print(data.min(), data.max())
print(fs)
print(librosa.get_duration(filename='xkocma08.wav'))
print(len(data))

t = np.arange(data.size) / fs
plt.figure(figsize=(6, 3))
plt.plot(t, data)

# plt.gca() vraci handle na aktualni Axes objekt,
# ktery nam umozni kontrolovat ruzne vlastnosti aktualniho grafu
# napr. popisy os
# viz https://matplotlib.org/users/pyplot_tutorial.html#working-with-multiple-figures-and-axes
plt.gca().set_xlabel('$t[s]$')
plt.gca().set_title('Zvukový signál')

plt.tight_layout()

# -------------------------------------------------------------------------
# 4.2 Ramce
# -------------------------------------------------------------------------

data = data/2**15  # normovani a ustredneni
E = np.mean(data, axis=0)
data = data - E
print("max = ",data.max(),"min = ", data.min())
j = 0
Matrix = []
for i in np.arange(1024, data.size, 512):
    norm_new = data[i-1024:i]
    Matrix.append(norm_new)
    # t = np.arange(norm_new.size) / fs
    # plt.figure(figsize=(6, 3))
    # plt.plot(t, norm_new)
    # plt.gca() vraci handle na aktualni Axes objekt,
    # ktery nam umozni kontrolovat ruzne vlastnosti aktualniho grafu
    # napr. popisy os
    # viz https://matplotlib.org/users/pyplot_tutorial.html#working-with-multiple-figures-and-axes
    # plt.gca().set_xlabel('$t[s]$')
    # plt.gca().set_title('Zvukový signál')

    # plt.tight_layout()
    # figura 16

t = np.arange(norm_new.size) / fs
plt.figure(figsize=(6, 3))
plt.plot(t, Matrix[15])
# plt.gca() vraci handle na aktualni Axes objekt,
# ktery nam umozni kontrolovat ruzne vlastnosti aktualniho grafu
# napr. popisy os
# viz https://matplotlib.org/users/pyplot_tutorial.html#working-with-multiple-figures-and-axes
plt.gca().set_xlabel('$t[s]$')
plt.gca().set_title('Zvukový signál')
plt.tight_layout()
#plt.show()
# -------------------------------------------------------------------------
# 4.3 DFT
# -------------------------------------------------------------------------
N = 1024
h = 0
g = 0
M = 1024
H = 1
my_fft = [[0 for i in range(M)] for j in range(H)]
for z in my_fft[0]:  # vypocet DTF
    h = 0
    for y in Matrix[15]:
        my_fft[0][g] = my_fft[0][g] + (Matrix[15][h] * (math.e ** (-(2j*math.pi/N)*h*g)))
        h = h + 1
    g = g + 1
my_fft_vec = np.array(my_fft)  # koeficienty DFT

norm_fft = np.fft.fft(Matrix[15])
d = np.allclose(norm_fft, my_fft_vec[0], 1.000001)  # vraci true
print(d)
k = np.zeros(N, dtype=int)
for l in range(N):
    k[l] = l
f = np.zeros(N, dtype=int)
for w in range(N):
    f[w] = (k[w]/N)*fs # frekvence na kterých leží

# moduly moji fft funkce
plt.figure(figsize=(6, 3))
plt.plot(f[:f.size//2+1], abs(my_fft_vec[0][:my_fft_vec.size//2+1]))
plt.gca()
# vraci handle na aktualni Axes objekt,
# ktery nam umozni kontrolovat ruzne vlastnosti aktualniho grafu
# napr. popisy os
# viz https://matplotlib.org/users/pyplot_tutorial.html#working-with-multiple-figures-and-axes
plt.gca().set_xlabel('$fs[Hz]$')
plt.gca().set_title('Moduly mojí DFT')

plt.tight_layout()

# moduly knihovni fft funkce
plt.figure(figsize=(6, 3))
plt.plot(f[:f.size//2+1], abs(norm_fft[:norm_fft.size//2+1]))
plt.gca()
# vraci handle na aktualni Axes objekt,
# ktery nam umozni kontrolovat ruzne vlastnosti aktualniho grafu
# napr. popisy os
# viz https://matplotlib.org/users/pyplot_tutorial.html#working-with-multiple-figures-and-axes
plt.gca().set_xlabel('$fs[Hz]$')
plt.gca().set_title('Moduly knihovní DFT')

plt.tight_layout()


# -------------------------------------------------------------------------
# 4.4 Spektogram
# -------------------------------------------------------------------------

f2, t2, sgr = spectrogram(data, fs, nperseg=1024, noverlap=512)
sgr_log = 10 * np.log10(sgr+1e-20)

plt.figure(figsize=(9,3))
plt.pcolormesh(t2,f2,sgr_log)
plt.gca().set_xlabel('Čas [s]')
plt.gca().set_ylabel('Frekvence [Hz]')
plt.gca().set_title('Spektrální hustota výkonu vstupního souboru')
cbar = plt.colorbar()
cbar.set_label('Spektralní hustota výkonu [dB]', rotation=270, labelpad=15)

plt.tight_layout()
#plt.show()

# -------------------------------------------------------------------------
# 4.5 Urceni rusivych funkci. Pouzivam metodu odecitani z jednoho spektra
# -------------------------------------------------------------------------
data_fft = np.fft.fft(data)
data_log = 10 * np.log10(np.abs(data_fft)**2)
f = np.arange(data_log.size) / len(data) * fs
plt.figure(figsize=(6, 3))
plt.plot(f[:f.size//2+1], data_log[:data_log.size//2+1])
plt.gca()
# vraci handle na aktualni Axes objekt,
# ktery nam umozni kontrolovat ruzne vlastnosti aktualniho grafu
# napr. popisy os
# viz https://matplotlib.org/users/pyplot_tutorial.html#working-with-multiple-figures-and-axes
plt.gca().set_xlabel('$fs[Hz]$')
plt.gca().set_title('Spektralní hustota výkonu [dB]')

plt.tight_layout()

# Zjistili jsme, že rušivé signály jsou f1=986Hz, f2=1972Hz, f3=2958Hz a f4=3944Hz
# také f2, f3 a f4 jsou násobky f1

# -------------------------------------------------------------------------
# 4.6 Generovani signalu
# -------------------------------------------------------------------------

rate = 16000
f4 = [986, 1972, 2958, 3944]
T = librosa.get_duration(filename='xkocma08.wav')

t = np.linspace(0, T, 25293, endpoint=False)
cos1 = np.cos(2*np.pi * f4[0] * t)
cos2 = np.cos(2*np.pi * f4[1] * t)
cos3 = np.cos(2*np.pi * f4[2] * t)
cos4 = np.cos(2*np.pi * f4[3] * t)
fsins = cos1 + cos2 + cos3 + cos4

wavio.write("4cos.wav", fsins, rate, sampwidth=2)
ob = soundfile.SoundFile("4cos.wav")
print(ob.subtype)
fs_4cos, data_4cos = wavfile.read('4cos.wav')
T = librosa.get_duration(filename='4cos.wav')
print(T)
print(fs_4cos)
f2, t2, sgr = spectrogram(data_4cos, fs_4cos, nperseg=1024, noverlap=512)
sgr_log = 10 * np.log10(sgr+1e-20)

plt.figure(figsize=(9,3))
plt.pcolormesh(t2,f2,sgr_log)
plt.gca().set_xlabel('Čas [s]')
plt.gca().set_ylabel('Frekvence [Hz]')
plt.gca().set_title('Spektralní hustota výkonu rušivého signálu')
cbar = plt.colorbar()
cbar.set_label('Spektralní hustota výkonu [dB]', rotation=270, labelpad=15)

plt.tight_layout()


# -------------------------------------------------------------------------
# 4.7 cistici filtr
# -------------------------------------------------------------------------

# filtr pro frekvenci 986 Hz
N1, Wn1 = signal.buttord([0.117, 0.1295], [0.121375, 0.125125], 3, 40, False)
b1, a1 = signal.butter(N1, Wn1, 'bandstop', False)
print("a1 = " ,a1)
print("b1 = " ,b1)
z1, p1, k1 = tf2zpk(b1, a1)
is_stable = (p1.size == 0) or np.all(np.abs(p1) < 1)
print('Filtr 1 {} stabilní.'.format('je' if is_stable else 'není'))

N_imp = 32
imp = [1, *np.zeros(N_imp-1)]
h1 = lfilter(b1, a1, imp)

plt.figure(figsize=(5,3))
plt.stem(np.arange(N_imp), h1, basefmt=' ')
plt.gca().set_xlabel('$n$')
plt.gca().set_title('Impulsní odezva filtru 1 $h[n]$')

plt.grid(alpha=0.5, linestyle='--')

plt.tight_layout()

# filtr pro frekvenci 1972 Hz
N2, Wn2 = signal.buttord([0.24025, 0.25275], [0.244625, 0.248375], 3, 40, False)
b2, a2 = signal.butter(N2, Wn2, 'bandstop', False)
print()
print("a2 = " ,a2)
print("b2 = " ,b2)

z2, p2, k2 = tf2zpk(b2, a2)
is_stable = (p2.size == 0) or np.all(np.abs(p2) < 1)
print('Filtr 2 {} stabilní.'.format('je' if is_stable else 'není'))

N_imp = 32
imp = [1, *np.zeros(N_imp-1)]
h2 = lfilter(b2, a2, imp)

plt.figure(figsize=(5,3))
plt.stem(np.arange(N_imp), h2, basefmt=' ')
plt.gca().set_xlabel('$n$')
plt.gca().set_title('Impulsní odezva filtru 2 $h[n]$')

plt.grid(alpha=0.5, linestyle='--')
plt.tight_layout()

# filtr pro frekvenci 2958 Hz
N3, Wn3 = signal.buttord([0.3635, 0.376], [0.367875, 0.371625], 3, 40, False)
b3, a3 = signal.butter(N3, Wn3, 'bandstop', False)

print()
print("a3 = " ,a3)
print("b3 = " ,b3)

z3, p3, k3 = tf2zpk(b3, a3)
is_stable = (p3.size == 0) or np.all(np.abs(p3) < 1)
print('Filtr 3 {} stabilní.'.format('je' if is_stable else 'není'))

N_imp = 32
imp = [1, *np.zeros(N_imp-1)]
h3 = lfilter(b3, a3, imp)

plt.figure(figsize=(5,3))
plt.stem(np.arange(N_imp), h3, basefmt=' ')
plt.gca().set_xlabel('$n$')
plt.gca().set_title('Impulsní odezva filtru 3 $h[n]$')

plt.grid(alpha=0.5, linestyle='--')

plt.tight_layout()

# filtr pro frekvenci 3944 Hz
N4, Wn4 = signal.buttord([0.48675, 0.49925], [0.491125, 0.494875], 3, 40, False)
b4, a4 = signal.butter(N4, Wn4, 'bandstop', False)

print()
print("a4 = " ,a4)
print("b4 = " ,b4)

z4, p4, k4 = tf2zpk(b4, a4)
is_stable = (p4.size == 0) or np.all(np.abs(p4) < 1)
print('Filtr 4 {} stabilní.'.format('je' if is_stable else 'není'))

N_imp = 32
imp = [1, *np.zeros(N_imp-1)]
h4 = lfilter(b4, a4, imp)

plt.figure(figsize=(5,3))
plt.stem(np.arange(N_imp), h4, basefmt=' ')
plt.gca().set_xlabel('$n$')
plt.gca().set_title('Impulsní odezva filtru 4 $h[n]$')

plt.grid(alpha=0.5, linestyle='--')

plt.tight_layout()


# -------------------------------------------------------------------------
# 4.8 Nulove body a poly
# -------------------------------------------------------------------------
plt.figure(figsize=(4,3.5))

# jednotkova kruznice
ang = np.linspace(0, 2*np.pi,100)
plt.plot(np.cos(ang), np.sin(ang))

# nuly, poly
plt.scatter(np.real(z4), np.imag(z4), marker='o', facecolors='none', edgecolors='r', label='nuly') # menit na 2,3 a 4
plt.scatter(np.real(p4), np.imag(p4), marker='x', color='g', label='póly')

plt.gca().set_title('Nuly a póly filtru 4 $h[n]$')
plt.gca().set_xlabel('Realná složka $\mathbb{R}\{$z$\}$')
plt.gca().set_ylabel('Imaginarní složka $\mathbb{I}\{$z$\}$')

plt.grid(alpha=0.5, linestyle='--')
plt.legend(loc='upper right')

plt.tight_layout()


# -------------------------------------------------------------------------
# 4.9 Frekvencni charakteristika
# -------------------------------------------------------------------------
w1, H1 = freqz(b1, a1)
_, ax = plt.subplots(1, 2, figsize=(8,3))

ax[0].plot(w1 / 2 / np.pi * fs, np.abs(H1))
ax[0].set_xlabel('Frekvence [Hz]')
ax[0].set_title('Modul frekvenční charakteristiky filtru 1 $|H(e^{j\omega})|$')

ax[1].plot(w1 / 2 / np.pi * fs, np.angle(H1))
ax[1].set_xlabel('Frekvence [Hz]')
ax[1].set_title('Argument frekvenční charakteristiky filtru 1 $\mathrm{arg}\ H(e^{j\omega})$')

for ax1 in ax:
    ax1.grid(alpha=0.5, linestyle='--')

plt.tight_layout()

w2, H2 = freqz(b2, a2)
_, ax = plt.subplots(1, 2, figsize=(8,3))

ax[0].plot(w2 / 2 / np.pi * fs, np.abs(H2))
ax[0].set_xlabel('Frekvence [Hz]')
ax[0].set_title('Modul frekvenční charakteristiky filtru 2 $|H(e^{j\omega})|$')

ax[1].plot(w2 / 2 / np.pi * fs, np.angle(H2))
ax[1].set_xlabel('Frekvence [Hz]')
ax[1].set_title('Argument frekvenční charakteristiky filtru 2 $\mathrm{arg}\ H(e^{j\omega})$')

for ax1 in ax:
    ax1.grid(alpha=0.5, linestyle='--')

plt.tight_layout()

w3, H3 = freqz(b3, a3)
_, ax = plt.subplots(1, 2, figsize=(8,3))

ax[0].plot(w3 / 2 / np.pi * fs, np.abs(H3))
ax[0].set_xlabel('Frekvence [Hz]')
ax[0].set_title('Modul frekvenční charakteristiky filtru 3 $|H(e^{j\omega})|$')

ax[1].plot(w3 / 2 / np.pi * fs, np.angle(H3))
ax[1].set_xlabel('Frekvence [Hz]')
ax[1].set_title('Argument frekvenční charakteristiky filtru 3 $\mathrm{arg}\ H(e^{j\omega})$')

for ax1 in ax:
    ax1.grid(alpha=0.5, linestyle='--')

plt.tight_layout()

w4, H4 = freqz(b4, a4)
_, ax = plt.subplots(1, 2, figsize=(8,3))

ax[0].plot(w4 / 2 / np.pi * fs, np.abs(H4))
ax[0].set_xlabel('Frekvence [Hz]')
ax[0].set_title('Modul frekvenční charakteristiky filtru 4 $|H(e^{j\omega})|$')

ax[1].plot(w4 / 2 / np.pi * fs, np.angle(H4))
ax[1].set_xlabel('Frekvence [Hz]')
ax[1].set_title('Argument frekvenční charakteristiky filtru 4 $\mathrm{arg}\ H(e^{j\omega})$')

for ax1 in ax:
    ax1.grid(alpha=0.5, linestyle='--')

plt.tight_layout()


# -------------------------------------------------------------------------
# 4.10 Filtrace
# -------------------------------------------------------------------------
h1_filt = lfilter(b1, a1, data)
h2_filt = lfilter(b2, a2, h1_filt)
h3_filt = lfilter(b3, a3, h2_filt)
h4_filt = lfilter(b4, a4, h3_filt)


wavio.write("clean_bandstop.wav", h4_filt, rate, sampwidth=2)
ob = soundfile.SoundFile("clean_bandstop.wav")
print(ob.subtype)
E = np.mean(h4_filt, axis=0)
h4_filt = h4_filt - E
t = np.arange(h4_filt.size) / fs
plt.figure(figsize=(6, 3))
plt.plot(t, h4_filt)

# plt.gca() vraci handle na aktualni Axes objekt,
# ktery nam umozni kontrolovat ruzne vlastnosti aktualniho grafu
# napr. popisy os
# viz https://matplotlib.org/users/pyplot_tutorial.html#working-with-multiple-figures-and-axes
plt.gca().set_xlabel('$t[s]$')
plt.gca().set_title('Zvukový signál')

plt.tight_layout()

f3, t3, sgr2 = spectrogram(h4_filt, fs, nperseg=1024, noverlap=512)
sgr_log2 = 10 * np.log10(sgr2+1e-20)

plt.figure(figsize=(9,3))
plt.pcolormesh(t3,f3,sgr_log2)
plt.gca().set_xlabel('Čas [s]')
plt.gca().set_ylabel('Frekvence [Hz]')
plt.gca().set_title('Spektrální hustota výkonu vstupního souboru')
cbar = plt.colorbar()
cbar.set_label('Spektralní hustota výkonu [dB]', rotation=270, labelpad=15)

plt.tight_layout()
#plt.show()

