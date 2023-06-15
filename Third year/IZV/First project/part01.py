#!/usr/bin/env python3
"""
IZV cast1 projektu
Autor: David Kocman, xkocma08

Detailni zadani projektu je v samostatnem projektu e-learningu.
Nezapomente na to, ze python soubory maji dane formatovani.

Muzete pouzit libovolnou vestavenou knihovnu a knihovny predstavene na prednasce
"""
from typing import List

import requests
from bs4 import BeautifulSoup
import numpy as np
import matplotlib.pyplot as plt

def integrate(x: np.array, y: np.array) -> float:
    """Computes integral"""
    arr = (x[1::] - x[:-1:]) * ((y[:-1:] + y[1::])/2)
    return np.sum(arr)


def generate_graph(a: List[float], show_figure: bool = False, save_path: str = None):
    """Generates graph of function fa(x) = a * x^2"""
    # Setup x axis and convert a to numpy array for better handling
    x = np.arange(-3.0, 3.01, 0.01)
    a = np.array(a)

    # Compute y axis values for the corresponding a
    # Produced 2D array
    vals = np.array(a[:, None] * x ** 2)

    _, (ax) = plt.subplots(nrows=1, constrained_layout=True, figsize=(8, 4))

    ax.set_xlabel("x")
    ax.set_ylabel(r"$f_a(x)$")
    ax.set_ylim(-20, 20)
    ax.set_xlim(-3, 3.8)
    ax.set_xticks([-3, -2, -1, 0, 1, 2, 3])

    plt.margins(x=0)

    # Plot the graph lines
    for i in range(len(vals)):
        # Color the area between line and 0
        plt.fill_between(x, vals[i], 0, alpha=0.15)
        ax.plot(x, vals[i],
                label=r'$\gamma_{%.1f}(x)$'%float(a[i]))

        # Add a text label next to the line
        plt.text(x=x[-1], y=vals[i][-1],\
                 s=r"$\int f_{%.1f}(x)dx$"%float(a[i]), va='center')

    plt.legend(loc='center', bbox_to_anchor=(0.5, 1.07), ncol=3)

    if show_figure:
        plt.show()
    if save_path is not None:
        plt.savefig(save_path, bbox_inches="tight", pad_inches=1)



def generate_sinus(show_figure: bool = False, save_path: str = None):
    """Generates three graphs of sin: f1, f2 and f1 + f2"""
    # Setup x axis
    t = np.arange(0, 100.01, 0.01)

    # Compute each individual function values
    f1 = 0.5 * np.sin((1/50) * np.pi * t)
    f2 = 0.25 * np.sin(np.pi * t)
    f3 = np.add(f1, f2)

    # Divide the canvas to three subplots and set their shared settings
    _, ax = plt.subplots(ncols=1,
                         nrows=3,
                         constrained_layout=True,
                         figsize=(9, 8))

    plt.setp(ax,
             xlim=(0, 100),
             yticks=([-0.8, -0.4, 0, 0.4, 0.8]),
             ylim=(-0.8, 0.8))

    (ax1, ax2, ax3) = ax

    # Plot
    ax1.set_xlabel("t")
    ax1.set_ylabel(r'$f_1(x)$')
    ax1.plot(t, f1)

    ax2.set_xlabel("t")
    ax2.set_ylabel(r'$f_2(x)$')
    ax2.plot(t, f2)

    ax3.set_xlabel("t")
    ax3.set_ylabel(r'$f_1(x) + f_2(x)$')

    # Divide trird function for the purpose of conditional coloring
    f3_gt = f3.copy()
    f3_lt = f3.copy()
    f3_gt[f3_gt >= f1] = np.nan
    f3_lt[f3_lt < f1] = np.nan

    ax3.plot(t, f3_gt, color="g")
    ax3.plot(t, f3_lt, color="r")

    if show_figure:
        plt.show()
    if save_path is not None:
        plt.savefig(save_path, bbox_inches="tight", pad_inches=1)


def download_data(url="https://ehw.fit.vutbr.cz/izv/temp.html"):
    """Downloads data from given URL"""
    # Download HTML of the given page.
    result = requests.get(url)

    soup = BeautifulSoup(result.text, features="xml")
    # Find all table elements.
    table = soup.find_all("tr")

    keys = ("year", "month", "temp")
    dictionary = {}
    index = 0
    cnt = 0

    # Loop through the table
    for tr in table:
        temps = np.array([])
        data = dict.fromkeys(keys)
        for td in tr.find_all("td"):
            # If the value if valid then fill up the dictionary.
            if td.p is not None:
                if cnt == 0:
                    data["year"] = int(td.p.text)
                elif cnt == 1:
                    data["month"] = int(td.p.text)
                else:
                    temps = np.append(temps, float(td.p.text.replace(',', '.')))
                cnt += 1
            #
        #
        # Construct the 2D dictionary.
        data["temp"] = temps
        dictionary[index] = data
        index += 1
        cnt = 0
    #
    return dictionary
#

def get_avg_temp(data, year=None, month=None) -> float:
    """Computes average temperature value in certain year and month if given"""
    # Setup of the lambda function for the filter.
    filt = lambda elem: (elem[1]["year"] == year and elem[1]["month"] == month) or\
                        (year is None and elem[1]["month"] == month) or\
                        (month is None and elem[1]["year"] == year) or\
                        (year is None and month is None)

    # Filter
    filtered = dict(filter(filt, data.items()))
    count = 1
    summ = 0

    # Make a list of all values from numpy arrays.
    values = np.concatenate([x["temp"] for x in filtered.values()])

    # Sum of all the temperatures and their total amount.
    summ = np.sum(values)
    count = len(values)

    # Compute the average temperature by dividing sum by the total count.
    # Provided that dictionary was filtered correctly.
    # If not, summ and count are None. In this case function returns 0.
    if summ and count is not None:
        value = np.divide(summ, count)
    else:
        value = 0

    return value


# first assignment
X = np.array([2, 4, 6, 8, 10, 12])
Y = np.array([3, 4, 6, 7, 8, 9])
VAL = integrate(X, Y)

# second assignment
generate_graph([1., 2., -2.], show_figure=False, save_path="tmp_fn.png")

# third assignment
generate_sinus(False, save_path="graf.png")

# fourth assignment
DICTIONARY = download_data()

# fifth assignment
VALUE = get_avg_temp(DICTIONARY, month=5)
