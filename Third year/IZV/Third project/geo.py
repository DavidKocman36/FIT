#!/usr/bin/python3.10
# coding=utf-8
""" Modul pro kresleni grafu s geofrafickymi daty """
import pandas as pd
import geopandas
import matplotlib.pyplot as plt
import contextily
from sklearn.cluster import MiniBatchKMeans
import numpy as np
# muzete pridat vlastni knihovny

# Author: David Kocman, xkocma08

def make_geo(df: pd.DataFrame) -> geopandas.GeoDataFrame:
    """ Konvertovani dataframe do geopandas.GeoDataFrame se spravnym kodovanim"""
    # prevedeni do geodataframe, kde sloupce d a e jsou souradnice
    gdf = geopandas.GeoDataFrame(df, geometry=geopandas.points_from_xy(df["d"], df["e"]), crs="EPSG:5514")
    # vymazani vsech neznamych lokaci
    gdf = gdf[gdf["d"].notna() & gdf["e"].notna()]

    return gdf

def plot_geo(gdf: geopandas.GeoDataFrame, fig_location: str = None, show_figure: bool = False):
    """ Vykresleni grafu s nehodami s alkoholem pro roky 2018-2021 """
    # pro zlinsky kraj
    gdf = gdf.loc[(gdf["region"] == "ZLK") & (gdf["p11"] >= 3), :]
    gdf_copy = gdf.copy()

    gdf_copy["p2a"] = pd.to_datetime(gdf["p2a"], errors='coerce')

    fig, axes = plt.subplots(2, 2, figsize=(10, 8), sharey=False, gridspec_kw={'width_ratios': [4, 4]})

    for i in range(2):
        for j in range(2):
            axes[i, j].axis("off")

    # vykreslovani grafu podle let
    # prvni graf je o trochu vetsi kvuli vetsi plose sázených souřadnic
    gdf_2018 = gdf_copy.loc[(gdf_copy['p2a'] > '2018-01-01') & (gdf_copy['p2a'] < '2018-12-31'), :]
    gdf_2018.plot(ax=axes[0, 0], markersize=5, aspect=1)
    axes[0, 0].title.set_text("2018")

    gdf_2019 = gdf_copy.loc[(gdf_copy['p2a'] > '2019-01-01') & (gdf_copy['p2a'] < '2019-12-31'), :]
    gdf_2019.plot(ax=axes[0, 1], markersize=5, aspect=1)
    axes[0, 1].title.set_text("2019")

    gdf_2020 = gdf_copy.loc[(gdf_copy['p2a'] > '2020-01-01') & (gdf_copy['p2a'] < '2020-12-31'), :]
    gdf_2020.plot(ax=axes[1, 0], markersize=5, aspect=1)
    axes[1, 0].title.set_text("2020")

    gdf_2021 = gdf_copy.loc[(gdf_copy['p2a'] > '2021-01-01') & (gdf_copy['p2a'] < '2021-12-31'), :]
    gdf_2021.plot(ax=axes[1, 1], markersize=5, aspect=1)
    axes[1, 1].title.set_text("2021")

    fig.suptitle("Pozice nehod ve Zlínském kraji kde hrály významnou roli alkohol a drogy", y=0.95)

    # pridani mapy do pozadi
    contextily.add_basemap(axes[0, 0], crs=gdf.crs.to_string(), source=contextily.providers.Stamen.TonerLite)
    contextily.add_basemap(axes[0, 1], crs=gdf.crs.to_string(), source=contextily.providers.Stamen.TonerLite)
    contextily.add_basemap(axes[1, 0], crs=gdf.crs.to_string(), source=contextily.providers.Stamen.TonerLite)
    contextily.add_basemap(axes[1, 1], crs=gdf.crs.to_string(), source=contextily.providers.Stamen.TonerLite)

    if fig_location is not None:
        plt.savefig(fig_location)
    if show_figure:
        plt.show()

def plot_cluster(gdf: geopandas.GeoDataFrame, fig_location: str = None, show_figure: bool = False):
    """ Vykresleni grafu s lokalitou vsech nehod v kraji shlukovanych do clusteru """

    gdf = gdf.loc[((gdf["p36"] == 1) | (gdf["p36"] == 3) | (gdf["p36"] == 2)) & (gdf["region"] == "ZLK"), :]
    gdf_copy = gdf.copy()

    # ziskani souradnic jako dvojce x,y
    coords = np.dstack([gdf_copy.geometry.x, gdf_copy.geometry.y]).reshape(-1, 2)

    # vypocitani kmeans clusteringu
    kmeans = MiniBatchKMeans(n_clusters=13, random_state=0).fit(coords)

    # rozdeleni bodu do clusteru a nasledne agregovani
    gdf_copy["cluster"] = kmeans.labels_
    gdf_copy = gdf_copy.dissolve(by="cluster", aggfunc={"cluster": "count"}
                                ).rename(columns=dict(cluster="cnt"))

    # vykresleni grafu
    _, ax = plt.subplots(1, 1, figsize=(10, 7))
    gdf_copy.plot(ax=ax, markersize=1, aspect=1, cmap="viridis", column="cnt", legend=True)
    contextily.add_basemap(ax, crs=gdf.crs.to_string(), source=contextily.providers.Stamen.TonerLite)
    plt.axis('off')
    plt.title("Četnost nehod na silnicích 1., 2. a 3. třídy ve Zlínském kraji")

    if fig_location is not None:
        plt.savefig(fig_location)
    if show_figure:
        plt.show()

if __name__ == "__main__":
    # zde muzete delat libovolne modifikace
    GDF = make_geo(pd.read_pickle("accidents.pkl.gz"))
    plot_geo(GDF, "geo1.png", True)
    plot_cluster(GDF, "geo2.png", True)
