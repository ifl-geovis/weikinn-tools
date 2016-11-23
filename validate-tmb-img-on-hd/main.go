package main

import (
	"bufio"
	"fmt"
	"log"
	"os"
	"sort"
	"strings"
)

func main() {

	// set working dir
	os.Chdir("../temp/weikinn")
	root, _ := os.Getwd()

	// Hard disk file
	// read in file
	file, err := os.Open(root + "/dateinamen.txt")
	if err != nil {
		log.Fatal(err)
	}
	defer file.Close()

	// create slice to store img names from harddisk
	var imageNamesHd []string

	scanner := bufio.NewScanner(file)
	for scanner.Scan() {
		imgNamesRaw := strings.Split(scanner.Text(), "\\")[3]
		imageNamesHd = append(imageNamesHd, imgNamesRaw)
	}

	if err := scanner.Err(); err != nil {
		log.Fatal(err)
	}

	sort.Strings(imageNamesHd)

	// Tambora file
	// read in tambora file
	tamboraFile, err := os.Open(root + "/weikinn_abfrage.csv")
	if err != nil {
		log.Fatal(err)
	}
	defer tamboraFile.Close()

	var imageNamesTmb []string

	// scan tamboraFile by line and do check for every img
	scannerTmb := bufio.NewScanner(tamboraFile)
	for scannerTmb.Scan() {
		if len(scannerTmb.Text()) > 0 {
			imgNamesRaw := strings.TrimPrefix(scannerTmb.Text(), "IMAGEFILES;;")
			imgNames := strings.Split(imgNamesRaw, ";;")
			for _, name := range imgNames {
				imageNamesTmb = append(imageNamesTmb, name)
			}
		}

	}

	sort.Strings(imageNamesTmb)

	if err := scannerTmb.Err(); err != nil {
		log.Fatal(err)
	}

	for _, v := range imageNamesTmb {
		target := v
		i := sort.Search(len(imageNamesHd),
			func(i int) bool { return imageNamesHd[i] >= target })
		if i < len(imageNamesHd) && imageNamesHd[i] == target {
			//fmt.Printf("found \"%s\" at imageNamesHd[%d]\n", imageNamesHd[i], i)
		} else {
			fmt.Printf("%s not found \n", target)
		}
	}
}
